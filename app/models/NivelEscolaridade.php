<?php

namespace App\models;

use App\core\Database;
use PDO;

class NivelEscolaridade
{
    public static function buscarTodos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, escolaridade as nome FROM pss.escolaridade ORDER BY escolaridade");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


