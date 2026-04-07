<?php

namespace App\models;

use App\core\Database;

class Naturalidade
{
    public static function buscarNaturalidade($estado_id = null)
    {
        $db = Database::getInstance();
        
        if ($estado_id) {
            $stmt = $db->prepare("SELECT id, cidade FROM naturalidade WHERE estado_id = :estado_id ORDER BY cidade");
            $stmt->execute(['estado_id' => $estado_id]);
        } else {
            $stmt = $db->prepare("SELECT id, cidade FROM naturalidade ORDER BY cidade");
            $stmt->execute();
        }
        
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    public static function buscarPorEstado($estado_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, cidade FROM naturalidade WHERE estado_id = :estado_id ORDER BY cidade");
        $stmt->execute(['estado_id' => $estado_id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    public static function buscarTodas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, cidade, estado_id FROM naturalidade ORDER BY cidade");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
