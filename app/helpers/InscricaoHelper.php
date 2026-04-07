<?php

namespace App\helpers;

use App\core\Database;
use PDO;

class InscricaoHelper
{
    public static function contarTodas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM pss.inscricao");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

