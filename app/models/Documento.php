<?php

namespace App\models;

use App\core\Database;
use PDO;

class Documento
{
    public static function criar($dados)
    {
        $sql = "INSERT INTO pss.documento (
            inscricao_id,
            tipo,
            nome_arquivo,
            caminho_arquivo,
            tamanho_bytes,
            mime_type,
            status_validacao,
            observacoes
        ) VALUES (
            :inscricao_id,
            :tipo,
            :nome_arquivo,
            :caminho_arquivo,
            :tamanho_bytes,
            :mime_type,
            :status_validacao,
            :observacoes
        )";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarPorInscricao($inscricao_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.documento_enviado WHERE inscricao_id = :inscricao_id ORDER BY criado_em ASC");
        $stmt->execute(['inscricao_id' => $inscricao_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.documento WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.documento SET 
            status_validacao = :status_validacao,
            observacoes = :observacoes
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function atualizarStatus($id, $status, $observacoes = null)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE pss.documento 
            SET status_validacao = :status, observacoes = :observacoes 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id, 
            'status' => $status, 
            'observacoes' => $observacoes
        ]);
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.documento WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function buscarPendentesValidacao()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT d.*, i.protocolo, c.nome as candidato_nome, c.cpf as candidato_cpf
            FROM pss.documento d
            JOIN pss.inscricao i ON d.inscricao_id = i.id
            JOIN pss.candidato c ON i.candidato_id = c.id
            WHERE d.status_validacao = 'pendente'
            ORDER BY d.criado_em ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorStatus($status)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM pss.documento WHERE status_validacao = :status");
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public static function buscarPorTipo($inscricao_id, $tipo)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM pss.documento 
            WHERE inscricao_id = :inscricao_id AND tipo = :tipo
            ORDER BY criado_em DESC
            LIMIT 1
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id, 'tipo' => $tipo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function validarTiposObrigatorios($inscricao_id, $tipos_obrigatorios)
    {
        $db = Database::getInstance();
        $placeholders = str_repeat('?,', count($tipos_obrigatorios) - 1) . '?';
        $stmt = $db->prepare("
            SELECT tipo FROM pss.documento 
            WHERE inscricao_id = ? AND tipo IN ($placeholders)
            AND status_validacao != 'rejeitado'
        ");
        
        $params = array_merge([$inscricao_id], $tipos_obrigatorios);
        $stmt->execute($params);
        
        $tipos_enviados = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tipos_faltantes = array_diff($tipos_obrigatorios, $tipos_enviados);
        
        return empty($tipos_faltantes);
    }
}

