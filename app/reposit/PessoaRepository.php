<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Pessoa;
use PDO;

class PessoaRepository
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function create(Pessoa $pessoa): bool
    {
        $sql = "INSERT INTO pessoa 
            (id, nome, cpf, data_nascimento, genero, foto_url, filiacao, cmcpd_numero, ativo)
            VALUES 
            (gen_random_uuid(), :nome, :cpf, :data_nascimento, :genero, :foto_url, :filiacao, :cmcpd_numero, :ativo)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nome' => $pessoa->nome,
            ':cpf' => $pessoa->cpf,
            ':data_nascimento' => $pessoa->data_nascimento,
            ':genero' => $pessoa->genero,
            ':foto_url' => $pessoa->foto_url,
            ':filiacao' => $pessoa->filiacao,
            ':cmcpd_numero' => $pessoa->cmcpd_numero,
            ':ativo' => $pessoa->ativo
        ]);
    }

    public function findByCPF(string $cpf): ?Pessoa
    {
        $stmt = $this->conn->prepare("SELECT * FROM pessoa WHERE cpf = :cpf");
        $stmt->execute([':cpf' => $cpf]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Pessoa($data) : null;
    }

    public function findById(string $id): ?Pessoa
    {
        $stmt = $this->conn->prepare("SELECT * FROM pessoa WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Pessoa($data) : null;
    }
}