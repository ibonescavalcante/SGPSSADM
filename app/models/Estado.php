<?php

namespace App\models;

use App\core\Database;
use PDO;

class Estado
{
    public static function buscarUF()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, sigla, estado FROM pss.estados ORDER BY estado");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
