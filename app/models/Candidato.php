<?php

namespace App\models;

use App\core\Database;
use PDO;

class Candidato
{
    public static function cadastrar($dados)
    {
        // Construir SQL dinamicamente baseado nos campos enviados
        $campos = array_keys($dados);
        $placeholders = array_map(function($campo) { return ":$campo"; }, $campos);
        
        $sql = "INSERT INTO pss.candidato (" . implode(', ', $campos) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                c.*,
                g.nome as genero_nome,
                e.escolaridade as escolaridade_nome,
                n.pais as nacionalidade_nome,
                est.estado as naturalidade_estado_nome,
                cid.cidade as naturalidade_cidade_nome
            FROM pss.candidato c
            LEFT JOIN pss.generos g ON c.genero_id = g.id
            LEFT JOIN pss.escolaridade e ON c.escolaridade_id = e.id
            LEFT JOIN pss.nacionalidades n ON c.nacionalidade_id = n.id
            LEFT JOIN pss.estados est ON c.naturalidade_estado_id = est.id
            LEFT JOIN pss.naturalidade cid ON c.naturalidade_cidade_id = cid.id
            WHERE c.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorCpf($cpf)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.candidato WHERE cpf = :cpf");
        $stmt->execute(['cpf' => $cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function busca_CPF($cpf)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT cpf FROM pss.candidato WHERE cpf = :cpf");
        $stmt->execute(['cpf' => $cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function confirma_senha($cpf)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, senha_hash, nome, cpf FROM pss.candidato WHERE cpf = :cpf");
        $stmt->execute(['cpf' => $cpf]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function buscarPorEmail($email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.candidato WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        // Construir SQL de UPDATE dinamicamente
        $campos = array_keys($dados);
        $sets = array_map(function($campo) { return "$campo = :$campo"; }, $campos);
        
        $sql = "UPDATE pss.candidato SET " . implode(', ', $sets) . ", atualizado_em = now() WHERE id = :id";
        
        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function atualizarSenha($id, $senha_hash)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pss.candidato SET senha_hash = :senha_hash, atualizado_em = now() WHERE id = :id");
        return $stmt->execute(['id' => $id, 'senha_hash' => $senha_hash]);
    }

    public static function buscarTodos($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM pss.candidato ORDER BY criado_em DESC";
        if ($limit) {
            $sql .= " LIMIT :limit";
            if ($offset) {
                $sql .= " OFFSET :offset";
            }
        }

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarTotal()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM pss.candidato");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.candidato WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}


