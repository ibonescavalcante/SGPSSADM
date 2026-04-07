<?php

namespace App\models;

use App\core\Database;
use PDO;

class PssEtapa
{
    public static function criar($dados)
    {
        $sql = "INSERT INTO pss.pss_etapa (
            pss_id,
            nome,
            descricao,
            dt_ini,
            dt_fim,
            permite_recurso,
            rec_ini,
            rec_fim,
            peso
        ) VALUES (
            :pss_id,
            :nome,
            :descricao,
            :dt_ini,
            :dt_fim,
            :permite_recurso,
            :rec_ini,
            :rec_fim,
            :peso
        )";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarPorPss($pss_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss_etapa WHERE pss_id = :pss_id ORDER BY dt_ini ASC");
        $stmt->execute(['pss_id' => $pss_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss_etapa WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.pss_etapa SET 
            nome = :nome,
            descricao = :descricao,
            dt_ini = :dt_ini,
            dt_fim = :dt_fim,
            permite_recurso = :permite_recurso,
            rec_ini = :rec_ini,
            rec_fim = :rec_fim,
            peso = :peso
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.pss_etapa WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function buscarEtapasAbertas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT e.*, p.titulo as pss_titulo 
            FROM pss.pss_etapa e
            JOIN pss.pss p ON e.pss_id = p.id
            WHERE e.dt_ini <= CURRENT_TIMESTAMP 
            AND e.dt_fim >= CURRENT_TIMESTAMP
            ORDER BY e.dt_ini ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarEtapasRecurso()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT e.*, p.titulo as pss_titulo 
            FROM pss.pss_etapa e
            JOIN pss.pss p ON e.pss_id = p.id
            WHERE e.permite_recurso = true
            AND e.rec_ini <= CURRENT_TIMESTAMP 
            AND e.rec_fim >= CURRENT_TIMESTAMP
            ORDER BY e.rec_ini ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function buscarEtapasRecursoPorPss($pss_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM pss.pss_etapa 
            WHERE pss_id = :pss_id 
            AND permite_recurso = true
            ORDER BY dt_ini ASC
        ");
        $stmt->execute(['pss_id' => $pss_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

