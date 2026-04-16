# Modificações: sessão única do painel (`/dashboard`)

Este documento descreve as alterações implementadas para garantir **uma sessão ativa por usuário** no painel administrativo, usando uma **tabela separada** no PostgreSQL (sem novo campo em `pss.usuario`).

---

## Objetivo

- Ao autenticar de novo com a mesma conta em outro navegador ou dispositivo, o **token de sessão** no banco é atualizado.
- O cliente que ainda guarda o **token antigo** na `$_SESSION` deixa de coincidir com o banco e é **desconectado** na próxima requisição protegida.
- O identificador persistido **não** é o `session_id` do PHP, porque `session_regenerate_id(true)` roda periodicamente no middleware; o vínculo usa um **token estável** gerado no login (`bin2hex(random_bytes(32))`, 64 caracteres).

---

## Arquivos novos

### 1. `database/sql/001_usuario_sessao_dashboard.sql`

Script DDL para criar a tabela no schema `pss`:

| Coluna          | Tipo           | Descrição |
|-----------------|----------------|-----------|
| `usuario_id`    | `INTEGER` PK   | Referência a `pss.usuario(id)`, `ON DELETE CASCADE`. Uma linha por usuário. |
| `token`         | `VARCHAR(64)`  | Token hex de 64 caracteres (32 bytes aleatórios). |
| `criado_em`     | `TIMESTAMPTZ`  | Preenchido em `INSERT` / atualizado no `UPSERT` de login. |
| `ultimo_acesso` | `TIMESTAMPTZ`  | Atualizado no login e, no máximo, a cada **60 segundos** por sessão válida (throttle na validação). |

**Deploy:** executar o SQL no mesmo banco configurado em `.env` (`DB_*`), por exemplo:

```bash
psql -h … -U … -d … -f database/sql/001_usuario_sessao_dashboard.sql
```

Sem essa tabela, o login do painel captura exceção e exibe mensagem genérica de falha.

---

### 2. `app/models/UsuarioSessaoDashboard.php`

Modelo PDO alinhado a `App\core\Database` (mesmo `search_path` que `Usuario`, incluindo `pss`).

| Método | Função |
|--------|--------|
| `registrarOuAtualizar(int $usuarioId, string $token)` | `INSERT … ON CONFLICT (usuario_id) DO UPDATE` — novo login sobrescreve o token. |
| `obterTokenPorUsuario(int $usuarioId): ?string` | Lê o token atual no banco; `null` se não houver linha. |
| `removerPorUsuario(int $usuarioId)` | `DELETE` da linha (logout explícito com sessão válida). |
| `tocarUltimoAcesso(int $usuarioId)` | `UPDATE` só de `ultimo_acesso` (uso throttled). |

Todas as queries usam **prepared statements**.

---

## Arquivos alterados

### 3. `app/controllers/LoginDashboardController.php`

**Imports**

- `use App\models\UsuarioSessaoDashboard;`

**`index()`**

- Se a URL tiver `?sessao=substituida`, repassa à view `dashboard/login/page` a chave `erro` com texto explicando que a conta foi acessada em outro lugar (após redirect do `Router`).

**`logar()` (POST login)**

1. Após `Usuario::autentica_uauario` com sucesso, dentro de `try/catch`:
2. `session_regenerate_id(true)` se a sessão PHP já estiver ativa (mitigação de fixation de sessão).
3. Gera `$token = bin2hex(random_bytes(32))`.
4. `UsuarioSessaoDashboard::registrarOuAtualizar($id, $token)`.
5. Preenche `$_SESSION['user']` com `id`, `nome`, `username` e **`dashboard_sessao_token`**.
6. `SessionSecurity::regenerateDashboardCsrfToken()` e redirect para `/dashboard`.
7. Em falha (ex.: tabela inexistente, erro de DB): `error_log` + view de login com erro genérico.

**`logout()`**

- Só chama `UsuarioSessaoDashboard::removerPorUsuario` se **`estaLogadoDashboard()`** e **`validarVinculoSessaoDashboard()`** forem verdadeiros.
- **Motivo:** se um cliente “antigo” (token já invalidado por outro login) abrir `/dashboard/logout`, não pode apagar a linha do banco — isso deslogaria o usuário que acabou de assumir a sessão. Nesse caso apenas `destruirSessao()` local.
- Em seguida: `SessionSecurity::destruirSessao()` e redirect para `/dashboard/login`.

---

### 4. `app/middleware/SessionSecurity.php`

**Import**

- `use App\models\UsuarioSessaoDashboard;`

**Novo método: `validarVinculoSessaoDashboard(): bool`**

- Se **não** estiver logado no painel (`estaLogadoDashboard` falso), retorna **`true`** (no-op para quem não usa `user`).
- Exige `$_SESSION['user']['dashboard_sessao_token']` string com **64** caracteres.
- Busca token no banco com `obterTokenPorUsuario`; em erro de DB retorna `false` e registra log.
- Compara com **`hash_equals($dbToken, $sessToken)`** (comparação em tempo constante).
- Se bate: throttle de **60 s** usando `$_SESSION['_dashboard_sessao_db_touch']` e `UsuarioSessaoDashboard::tocarUltimoAcesso` quando necessário.

---

### 5. `app/routes/Router.php`

**Novo método privado: `responderSessaoDashboardSubstituida(string $uri): void`**

- Requisições **`/api/…`:** HTTP **401**, JSON `{ "erro": "Sessão encerrada. A conta foi acessada em outro local." }`.
- Demais (HTML): redirect **`/dashboard/login?sessao=substituida`** para exibir a mensagem no `LoginDashboardController::index`.

**`execute()` — ordem das guardas**

Após a verificação existente “rota do painel exige auth e não está logado”:

- Se a rota exige autenticação do painel **e** `estaLogadoDashboard()` **e** `!validarVinculoSessaoDashboard()`:
  - `SessionSecurity::destruirSessao()`
  - `responderSessaoDashboardSubstituida($uri)`

Assim, **todas** as URIs cobertas por `dashboardRequerAutenticacao` (incluindo POSTs do painel e rotas que usam `$_SESSION['user']['id']` no `ApiController`) passam pela validação do vínculo.

A checagem de **CSRF** permanece **depois** dessa etapa (comportamento anterior preservado para quem está com sessão válida).

---

## Dados de sessão relevantes

| Chave | Onde | Uso |
|-------|------|-----|
| `$_SESSION['user']['dashboard_sessao_token']` | Após login do painel | Deve coincidir com `pss.usuario_sessao_dashboard.token`. |
| `$_SESSION['_dashboard_sessao_db_touch']` | Durante navegação autenticada | Timestamp interno para não atualizar `ultimo_acesso` a cada request. |

Sessões antigas (antes do deploy) sem `dashboard_sessao_token` válido falham na validação e são redirecionadas com `sessao=substituida` / 401 em API — **comportamento esperado** após a migração (forçar novo login).

---

## Fluxo resumido

1. **Login:** UPSERT do token no banco + token na sessão.
2. **Request protegido:** compara token sessão vs banco; segundo login altera só o banco → primeiro cliente perde o vínculo.
3. **Logout consciente com sessão válida:** remove a linha no banco e destrói a sessão PHP.
4. **Logout / navegação com sessão já invalidada:** não remove a linha do banco; só limpa sessão local se aplicável.

---

## Testes manuais sugeridos

1. Aplicar o SQL e fazer login no painel.
2. Mesmo usuário em outro perfil/navegador: segundo login OK.
3. Voltar ao primeiro navegador, atualizar `/dashboard`: redirect para login com mensagem de sessão substituída.
4. Logout apenas no cliente **válido**: linha removida; novo login recria registro.
5. Chamada `fetch`/POST a `/api/…` com cookie do cliente invalidado: **401** com JSON de sessão encerrada.

---

## Possíveis evoluções (não implementadas)

- Invalidar a linha em `usuario_sessao_dashboard` ao **alterar senha** (`Usuario::atualizarSenha`), se desejarem revogar todas as sessões após troca de credencial.

---

## Ver também

Outras alterações do painel no mesmo projeto (roteamento, CSRF, APIs, **criação de utilizadores em Configurações**) estão resumidas em [CHANGELOG-HARDENING.md](../CHANGELOG-HARDENING.md) (secções 1–5, 8 e 9 e “Como validar”).
