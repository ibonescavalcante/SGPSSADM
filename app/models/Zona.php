<?php

namespace App\models;

use App\core\Database;
use PDO;

class Zona
{
    public static function buscarTodos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, nome FROM pss.zona ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


