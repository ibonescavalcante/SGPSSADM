<?php

namespace App\models;

use App\core\Database;
use PDO;

class TipoDocumento
{
    public static function buscarTodos()
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id, codigo, nome FROM pss.tipos_documento WHERE ativo = TRUE ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar tipos de documento: " . $e->getMessage());
            // Retornar array vazio em caso de erro
            return [];
        }
    }
}

