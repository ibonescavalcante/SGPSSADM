<?php

namespace App\Models;

class Pessoa
{
    public ?string $id = null;
    public string $nome;
    public string $cpf;
    public string $data_nascimento;
    public ?string $genero = null;
    public string $foto_url;
    public ?string $filiacao = null;
    public string $cmcpd_numero;
    public string $data_cadastro;
    public bool $ativo = true;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->nome = $data['nome'] ?? '';
        $this->cpf = $data['cpf'] ?? '';
        $this->data_nascimento = $data['data_nascimento'] ?? '';
        $this->genero = $data['genero'] ?? null;
        $this->foto_url = $data['foto_url'] ?? '';
        $this->filiacao = $data['filiacao'] ?? null;
        $this->cmcpd_numero = $data['cmcpd_numero'] ?? '';
        $this->data_cadastro = $data['data_cadastro'] ?? date('Y-m-d H:i:s');
        $this->ativo = $data['ativo'] ?? true;
    }
}