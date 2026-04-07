<?php

namespace App\models;

use App\core\Database;

class Nacionalidade
{
    public static function buscarNacionalidade()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id,pais FROM nacionalidades");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
