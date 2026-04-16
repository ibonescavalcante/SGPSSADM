<?php

namespace App\models;

use App\core\Database;
use PDO;

class Usuario
{
    /** Valores do enum pss.perfil_usuario (ajuste se o DDL do banco for diferente). */
    public const PERFIS_VALIDOS = ['comissao', 'administrador', 'visualizador'];

    public static function criar($dados)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO pss.usuario (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)");
        return $stmt->execute([
            'nome'       => $dados['nome'],
            'email'      => $dados['email'],
            'senha_hash' => $dados['senha_hash'],
        ]);
    }

    /**
     * Lista usuários para a tela de configurações do painel.
     *
     * @return list<array{id:int,nome:string,email:string,telefone:?string,perfil:string,ativo:bool,criado_em:?string}>
     */
    public static function listarParaPainel(): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT id, nome, email, telefone, perfil::text AS perfil, ativo, criado_em
                FROM pss.usuario
                ORDER BY nome ASC';
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }

    public static function emailJaCadastrado(string $email, ?int $excetoId = null): bool
    {
        $db = Database::getInstance();
        if ($excetoId !== null) {
            $stmt = $db->prepare(
                'SELECT 1 FROM pss.usuario WHERE lower(trim(email)) = lower(trim(:email)) AND id <> :id LIMIT 1'
            );
            $stmt->execute(['email' => $email, 'id' => $excetoId]);
        } else {
            $stmt = $db->prepare(
                'SELECT 1 FROM pss.usuario WHERE lower(trim(email)) = lower(trim(:email)) LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
        }
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Alinha a sequência do SERIAL/IDENTITY de `pss.usuario.id` ao maior id existente.
     * Evita erro 23505 em `usuario_pkey` quando a sequência ficou atrás (imports, inserts manuais, etc.).
     */
    private static function sincronizarSequenciaIdUsuario(\PDO $db): void
    {
        try {
            $seq = $db->query("SELECT pg_get_serial_sequence('pss.usuario', 'id')")->fetchColumn();
            if (!is_string($seq) || $seq === '') {
                return;
            }
            $quoted = $db->quote($seq);
            $db->exec(
                "SELECT setval({$quoted}::regclass, COALESCE((SELECT MAX(id) FROM pss.usuario), 0), true)"
            );
        } catch (\Throwable $e) {
            error_log('Usuario::sincronizarSequenciaIdUsuario: ' . $e->getMessage());
        }
    }

    public static function mensagemErroDuplicacao(\PDOException $e): string
    {
        $msg = $e->getMessage();
        $l = strtolower($msg);
        if (preg_match('/unique constraint "([^"]+)"/i', $msg, $m)) {
            $c = strtolower($m[1]);
            if (str_contains($c, 'email')) {
                return 'Este e-mail já está cadastrado.';
            }
            if (str_contains($c, 'telefone')) {
                return 'Este telefone já está associado a outro utilizador.';
            }
        }
        if (str_contains($l, 'pkey') || preg_match('/key \(id\)=/i', $msg)) {
            return 'Não foi possível criar o utilizador: conflito no ID (sequência desalinhada). Tente novamente; se continuar, peça ao administrador da base de dados para sincronizar a sequência da tabela pss.usuario.';
        }
        if (str_contains($l, 'email')) {
            return 'Este e-mail já está cadastrado.';
        }
        if (str_contains($l, 'telefone')) {
            return 'Este telefone já está associado a outro utilizador.';
        }

        return 'Não foi possível gravar: valor duplicado na base de dados.';
    }

    /**
     * Cria usuário completo conforme colunas atuais de pss.usuario.
     *
     * @param array{nome:string,email:string,telefone?:string,perfil:string,senha:string,ativo?:bool} $dados
     * @return int|false id do novo registro
     */
    public static function criarUsuarioPainel(array $dados)
    {
        $perfil = $dados['perfil'] ?? '';
        if (!is_string($perfil) || !in_array($perfil, self::PERFIS_VALIDOS, true)) {
            return false;
        }

        $nome = isset($dados['nome']) ? trim((string) $dados['nome']) : '';
        $email = isset($dados['email']) ? strtolower(trim((string) $dados['email'])) : '';
        $telefone = isset($dados['telefone']) ? trim((string) $dados['telefone']) : '';
        $senha = $dados['senha'] ?? '';
        if ($nome === '' || $email === '' || !is_string($senha) || $senha === '') {
            return false;
        }

        $ativo = array_key_exists('ativo', $dados) ? (bool) $dados['ativo'] : true;
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        if ($senhaHash === false) {
            return false;
        }

        $db = Database::getInstance();
        self::sincronizarSequenciaIdUsuario($db);

        $telefoneDb = $telefone === '' ? null : $telefone;

        $stmt = $db->prepare(
            'INSERT INTO pss.usuario (nome, email, telefone, perfil, senha_hash, ativo, criado_em, atualizado_em)
             VALUES (:nome, :email, :telefone, CAST(:perfil AS pss.perfil_usuario), :senha_hash, :ativo, NOW(), NOW())
             RETURNING id'
        );
        $ok = $stmt->execute([
            'nome'       => $nome,
            'email'      => $email,
            'telefone'   => $telefoneDb,
            'perfil'     => $perfil,
            'senha_hash' => $senhaHash,
            'ativo'      => $ativo,
        ]);

        if (!$ok) {
            return false;
        }

        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : false;
    }

    public static function buscarPorUauario($email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, nome, email, senha_hash, ativo, perfil::text AS perfil
             FROM pss.usuario
             WHERE lower(trim(email)) = lower(trim(:email))'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($row) && isset($row['perfil'])) {
            $row['perfil'] = strtolower(trim((string) $row['perfil']));
        }
        return $row;
    }

    /**
     * Perfil atual na BD (revalidação; mitiga manipulação da sessão).
     */
    public static function perfilPorId(int $id): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT perfil::text AS perfil FROM pss.usuario WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $p = strtolower(trim((string) ($row['perfil'] ?? '')));
        return $p !== '' ? $p : null;
    }

    /**
     * Dados para edição no painel (sem hash de senha).
     *
     * @return array{id:int,nome:string,email:string,telefone:?string,perfil:string,ativo:bool}|null
     */
    public static function buscarParaEdicaoPainel(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, nome, email, telefone, perfil::text AS perfil, ativo
             FROM pss.usuario WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $row['id'] = (int) $row['id'];
        $row['perfil'] = strtolower(trim((string) ($row['perfil'] ?? '')));
        $a = $row['ativo'] ?? false;
        $row['ativo'] = $a === true || $a === 't' || $a === '1' || $a === 1;
        $row['telefone'] = isset($row['telefone']) && $row['telefone'] !== null && $row['telefone'] !== ''
            ? (string) $row['telefone'] : null;
        return $row;
    }

    /**
     * Atualiza utilizador do painel. Senha: só altera se $dados['senha'] for string não vazia.
     *
     * @param array{nome:string,email:string,telefone?:string,perfil:string,ativo:bool,senha?:string,senha_confirmacao?:string} $dados
     */
    public static function atualizarUsuarioPainel(int $id, array $dados): bool
    {
        $perfil = isset($dados['perfil']) ? strtolower(trim((string) $dados['perfil'])) : '';
        if (!in_array($perfil, self::PERFIS_VALIDOS, true)) {
            return false;
        }

        $nome = isset($dados['nome']) ? trim((string) $dados['nome']) : '';
        $email = isset($dados['email']) ? strtolower(trim((string) $dados['email'])) : '';
        $telefone = isset($dados['telefone']) ? trim((string) $dados['telefone']) : '';
        $ativo = array_key_exists('ativo', $dados) ? (bool) $dados['ativo'] : true;
        $senha = isset($dados['senha']) ? (string) $dados['senha'] : '';
        $senha2 = isset($dados['senha_confirmacao']) ? (string) $dados['senha_confirmacao'] : '';

        if ($nome === '' || $email === '') {
            return false;
        }

        if ($senha !== '' || $senha2 !== '') {
            if (strlen($senha) < 8 || !hash_equals($senha, $senha2)) {
                return false;
            }
        }

        $db = Database::getInstance();
        $telefoneDb = $telefone === '' ? null : $telefone;

        $sql = 'UPDATE pss.usuario SET nome = :nome, email = :email, telefone = :telefone,
                perfil = CAST(:perfil AS pss.perfil_usuario), ativo = :ativo, atualizado_em = NOW()';
        $params = [
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefoneDb,
            'perfil'   => $perfil,
            'ativo'    => $ativo,
            'id'       => $id,
        ];

        if ($senha !== '') {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            if ($hash === false) {
                return false;
            }
            $sql .= ', senha_hash = :senha_hash';
            $params['senha_hash'] = $hash;
        }

        $sql .= ' WHERE id = :id';
        $stmt = $db->prepare($sql);

        return (bool) $stmt->execute($params);
    }
    public static function buscarNomePorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT nome FROM pss.usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT senha_hash FROM pss.usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // verifica se a senha atual e igual a do banco de dados 
    public static function verificarSenha($usuario_id, $senha)
    {
        $usuario = self::buscarPorId($usuario_id);
        if (!$usuario || empty($usuario['senha_hash'])) {
            return false;
        }
        $resultado = password_verify($senha, $usuario['senha_hash']);

        if ($resultado) {
            return $resultado;
        }
        return false;
    }
    public static function autentica_uauario($username, $senha)
    {
        $usuario = self::buscarPorUauario($username);

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            return false;
        }

        if (array_key_exists('ativo', $usuario)) {
            $a = $usuario['ativo'];
            if ($a === false || $a === 'f' || $a === '0' || $a === 0) {
                return false;
            }
        }

        return $usuario;
    }

    public static function atualizarSenha($id, $novaSenhaHash)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pss.usuario SET senha_hash = :senha_hash WHERE id = :id");
        return $stmt->execute([
            'senha_hash' => $novaSenhaHash,
            'id'         => $id
        ]);
    }
}
