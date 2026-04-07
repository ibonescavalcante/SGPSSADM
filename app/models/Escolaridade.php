<?php

namespace App\models;

use App\core\Database;

class Escolaridade
{
    public static function buscarEscolaridade()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id,escolaridade FROM escolaridade");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
