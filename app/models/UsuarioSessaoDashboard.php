<?php

namespace App\models;

use App\core\Database;
use PDO;

class UsuarioSessaoDashboard
{
    public static function registrarOuAtualizar(int $usuarioId, string $token): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO usuario_sessao_dashboard (usuario_id, token, criado_em, ultimo_acesso)
             VALUES (:usuario_id, :token, NOW(), NOW())
             ON CONFLICT (usuario_id) DO UPDATE SET
                 token = EXCLUDED.token,
                 criado_em = NOW(),
                 ultimo_acesso = NOW()'
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'token'      => $token,
        ]);
    }

    public static function obterTokenPorUsuario(int $usuarioId): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT token FROM usuario_sessao_dashboard WHERE usuario_id = :id'
        );
        $stmt->execute(['id' => $usuarioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !isset($row['token']) || !is_string($row['token'])) {
            return null;
        }
        return $row['token'];
    }

    public static function removerPorUsuario(int $usuarioId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'DELETE FROM usuario_sessao_dashboard WHERE usuario_id = :id'
        );
        $stmt->execute(['id' => $usuarioId]);
    }

    public static function tocarUltimoAcesso(int $usuarioId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'UPDATE usuario_sessao_dashboard SET ultimo_acesso = NOW() WHERE usuario_id = :id'
        );
        $stmt->execute(['id' => $usuarioId]);
    }
}
