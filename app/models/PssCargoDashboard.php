<?php

namespace App\models;

use App\core\Database;
use PDO;

class PssCargoDashboard
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function busca_cargo_by_processo_id($id)
    {
        // $sql = "SELECT id,nome_cargo FROM pss.pss_cargo WHERE pss_id = :pss_id ORDER BY nome ASC";
        // $sql = "SELECT DISTINCT nome_cargo FROM pss.pss_cargo WHERE pss_id = :pss_id ORDER BY nome ASC";
        $sql = "SELECT DISTINCT nome FROM pss.pss_cargo WHERE pss_id = :pss_id ORDER BY nome ASC;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pss_id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
