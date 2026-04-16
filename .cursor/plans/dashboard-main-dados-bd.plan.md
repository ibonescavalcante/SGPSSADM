---
name: Dashboard dados BD (MVC+Service+Repository)
overview: Popular a home do painel com dados reais do PostgreSQL seguindo obrigatoriamente o fluxo Controller → Service → Repository → Banco; modelos apenas como dados (sem SQL); sem acrescentar novas queries nas classes legado em app/models que hoje misturam persistência.
todos:
  - id: dto-models
    content: Criar modelos/DTOs só com propriedades (ex. resumo do painel + item de processo para a lista), sem SQL
  - id: repositories
    content: Criar repositórios em app/repositories com PDO (contagens e listagem PSS em_andamento), namespaces App\repositories
  - id: service-painel
    content: Criar Service em app/services que orquestra repositórios, monta o DTO e encapsula regras (ex. critério ativo, limite da lista)
  - id: controller-view
    content: DashboardController::index() chama apenas o Service e passa dados à view; atualizar main.php para exibir o DTO
---

# Plano: dados do BD na home do dashboard (MVC + Service + Repository)

Referência obrigatória: [.cursor/rules/regras-mvc.rules](.cursor/rules/regras-mvc.rules).

## Regras aplicáveis a esta entrega

- **Fluxo:** `Controller → Service → Repository → Banco` (nunca Controller → Model com SQL).
- **Model (`app/models`):** apenas propriedades / estrutura de dados; **proibido SQL** e regras de negócio nas classes novas criadas para o painel.
- **Repository (`app/repositories`):** único lugar para **SELECT/COUNT** usados nesta feature; usar `App\core\Database` (PDO) como no resto do projeto.
- **Service (`app/services`):** orquestra repositórios, aplica regras (ex.: limite de itens na lista, mapeamento de status para label), pode lançar exceções.
- **Controller:** HTTP + chamar Service + `view(..., $data)`; **sem SQL** e sem lógica pesada.
- **Evitar** novos métodos estáticos “para tudo”; preferir **instâncias** nos novos artefatos, alinhado à regra 6.
- **Legado:** classes como `PssDashboard`, `InscricaoDashboard` e `Candidato` **já contêm SQL** e não obedecem ao modelo ideal. Para esta tarefa **não** se acrescenta lógica nova nelas; o código novo fica em **Repository + Service**. Refatoração total dessas classes fica fora do escopo.

Autoload: `composer.json` já mapeia `App\\` → `app/`, logo `App\repositories\...` e `App\services\...` resolvem em `app/repositories/` e `app/services/` após criar as pastas.

## Situação da view e do controller

- [`app/views/dashboard/main.php`](app/views/dashboard/main.php): valores fixos e um processo estático; deve passar a consumir apenas variáveis vindas do Plates (sem acesso à BD na view).
- [`app/controllers/DashboardController.php`](app/controllers/DashboardController.php): `index()` hoje chama `view('dashboard/main')` sem dados; deve chamar um **Service** e repassar o array/DTO já preparado.

## Dados a expor (domínio já refletido no legado)

| Apresentação | Origem no BD | Responsável na arquitetura nova |
|--------------|--------------|-----------------------------------|
| Processos ativos / em andamento | `COUNT(*)` em `pss.pss` com `status_global = 'em_andamento'` | `PssRepository` (ou nome equivalente) |
| Candidatos | `COUNT(*)` em `pss.candidato` | `CandidatoRepository` |
| Inscrições | `COUNT(*)` em `pss.inscricao` com `status_inscricao = 'Ativa'` (coerente com [`InscricaoDashboard`](app/models/InscricaoDashboard.php)) | `InscricaoRepository` |
| Lista “Processos em Andamento” | Linhas de `pss.pss` (`titulo`, `inscricao_ini`, `inscricao_fim`, `status_global`, etc.) com filtro `em_andamento`, com **LIMIT** definido no Service/Repository | `PssRepository` + montagem no Service |

## Desenho de classes (proposta)

```mermaid
flowchart TB
  DC[DashboardController index]
  DS[DashboardPainelService ou nome acordado]
  PR[PssRepository]
  CR[CandidatoRepository]
  IR[InscricaoRepository]
  DTO[EstatisticasPainel ou DTO dedicado]
  V[main.php]
  DB[(PostgreSQL pss)]
  DC --> DS
  DS --> PR
  DS --> CR
  DS --> IR
  DS --> DTO
  DC --> V
  PR --> DB
  CR --> DB
  IR --> DB
```

1. **DTO / model de leitura (só dados)**  
   Ex.: classe `EstatisticasPainel` (ou `PainelInicialViewModel`) com propriedades tipadas: totais inteiros + `array` de objetos leves do tipo “processo em listagem” (id, titulo, datas, status_global). Sem construtor com query; pode ter construtor simples ou setters apenas se fizer sentido.

2. **Repositories**  
   - Métodos explícitos e pequenos, ex.: `contarPssPorStatusGlobal(string $status): int`, `listarPssPorStatusGlobal(string $status, int $limite, string $ordem): array`.  
   - Retornar arrays associativos ou instâncias dos DTOs mapeados no Service (preferência: array no Repository, montagem de DTO no Service — mantém Repository “burro”).

3. **Service**  
   - Ex.: `DashboardPainelService::obterResumo(): EstatisticasPainel`.  
   - Injeta ou instancia os três repositórios (ou um facade repository se preferirem um único arquivo para leitura do painel — ainda assim sem regra de negócio no Repository).  
   - Define `LIMITE_PROCESSOS_HOME` (constante ou config).  
   - Opcional: `try/catch` e retorno seguro (zeros + lista vazia) conforme política de erro do projeto.

4. **Controller**  
   - `$this->view('dashboard/main', $service->obterResumo()->toArray())` ou passar o objeto se o Plates aceitar propriedades públicas de forma conveniente.

5. **View**  
   - Substituir números fixos por escape seguro (`htmlspecialchars` / `number_format` pt-BR).  
   - `foreach` na lista de processos; datas formatadas na view ou já formatadas no Service (regra de apresentação: preferir uma única convenção).  
   - Link “Ver Todos” → [`/dashboard/processos`](app/routes/Router.php).  
   - Revisar fechamento de `</div>` no final de `main.php` para bater com o layout em [`template.php`](app/views/dashboard/template.php).

## Critérios de aceitação

- Nenhum SQL novo em `app/models` para esta feature.  
- `DashboardController::index` contém apenas chamada ao Service e renderização.  
- Repositórios contêm apenas acesso a dados; Service concentra orquestração e limites.  
- Home reflete contagens e lista reais da BD.

## Nota sobre migração futura

Extrair gradualmente o SQL de `PssDashboard` / `InscricaoDashboard` para repositórios pode ser feito em tarefas separadas; este plano limita-se a **caminho correto** para a home sem ampliar o débito técnico nas classes legadas.
