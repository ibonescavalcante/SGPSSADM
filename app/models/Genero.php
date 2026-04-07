<?php

namespace App\models;

use App\core\Database;
use PDO;

class Genero
{
    public static function buscarGeneros()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, nome FROM pss.generos ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
