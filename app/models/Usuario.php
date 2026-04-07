<?php

namespace App\models;

use App\core\Database;
use PDO;

class Usuario
{
    public static function criar($dados)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO pss.usuario (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)");
        return $stmt->execute([
            'nome'       => $dados['nome'],
            'email'      => $dados['email'],
            'senha_hash' => $dados['senha_hash'],
        ]);
    }

    public static function buscarPorUauario($email)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, nome, email, senha_hash FROM pss.usuario WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function buscarNomePorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT nome FROM pss.usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT senha_hash FROM pss.usuario WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // verifica se a senha atual e igual a do banco de dados 
    public static function verificarSenha($usuario_id, $senha)
    {
        $usuario = self::buscarPorId($usuario_id);
        $resultado = password_verify($senha, $usuario['senha_hash']);

        if ($resultado) {
            return $resultado;
        }
        return false;
    }
    public static function autentica_uauario($username, $senha)
    {
        $usuario = self::buscarPorUauario($username);

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            return $usuario;
        }
        return false;
    }

    public static function atualizarSenha($id, $novaSenhaHash)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pss.usuario SET senha_hash = :senha_hash WHERE id = :id");
        return $stmt->execute([
            'senha_hash' => $novaSenhaHash,
            'id'         => $id
        ]);
    }
}
