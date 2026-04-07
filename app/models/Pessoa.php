<?php

namespace App\models;

use App\core\Database;

class Pessoa
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM pessoa");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM pessoa WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO pessoa (nome, cpf_cnpj, endereco, cidade, estado, pais, telefone, email, site) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nome'],
            $data['cpf_cnpj'],
            $data['endereco'],
            $data['cidade'],
            $data['estado'],
            $data['pais'],
            $data['telefone'],
            $data['email'],
            $data['site']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE pessoa SET nome=?, cpf_cnpj=?, endereco=?, cidade=?, estado=?, pais=?, telefone=?, email=?, site=? WHERE id=?");
        return $stmt->execute([
            $data['nome'],
            $data['cpf_cnpj'],
            $data['endereco'],
            $data['cidade'],
            $data['estado'],
            $data['pais'],
            $data['telefone'],
            $data['email'],
            $data['site'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM pessoa WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
