<?php

namespace App\models;

use App\core\Database;
use PDO;

class CandidatoToken
{
    /**
     * Cria um novo token para o candidato
     */
    public static function criarToken($candidato_id, $duracao_minutos = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime("+{$duracao_minutos} minutes"));

        $sql = "INSERT INTO pss.candidato_token (candidato_id, token, token_expiracao) 
                VALUES (:candidato_id, :token, :token_expiracao)";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            'candidato_id' => $candidato_id,
            'token' => $token,
            'token_expiracao' => $expiracao
        ]);

        return $result ? $token : false;
    }

    /**
     * Valida um token e retorna os dados do candidato se válido
     */
    public static function validarToken($token)
    {
        $sql = "SELECT ct.*, c.nome, c.email, c.cpf 
                FROM pss.candidato_token ct
                JOIN pss.candidato c ON ct.candidato_id = c.id
                WHERE ct.token = :token 
                AND ct.token_expiracao > NOW() 
                AND ct.usado = FALSE";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Marca um token como usado
     */
    public static function marcarTokenUsado($token)
    {
        $sql = "UPDATE pss.candidato_token SET usado = TRUE WHERE token = :token";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute(['token' => $token]);
    }

    /**
     * Remove tokens expirados
     */
    public static function limparTokensExpirados()
    {
        $sql = "DELETE FROM pss.candidato_token WHERE token_expiracao < NOW()";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Invalida todos os tokens de um candidato
     */
    public static function invalidarTokensCandidato($candidato_id)
    {
        $sql = "UPDATE pss.candidato_token SET usado = TRUE WHERE candidato_id = :candidato_id";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute(['candidato_id' => $candidato_id]);
    }
}

