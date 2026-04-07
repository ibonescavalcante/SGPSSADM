<?php

namespace App\models;

use App\core\Database;
use PDO;

class CargaHoraria
{
    public static function buscarTodos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.carga_horaria ORDER BY horas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


