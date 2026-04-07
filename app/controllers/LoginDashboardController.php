<?php

namespace App\controllers;

use App\models\Usuario;
use App\middleware\SessionSecurity;


class LoginDashboardController extends Controller
{
    public function index()
    {
        $this->view('dashboard/login/page');
    }

    public function logar()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // $_POST é sempre um array
            $dados = $_POST;

            // Acesso correto para array
            $username = isset($dados['username']) ? trim($dados['username']) : '';
            $password = isset($dados['password']) ? trim($dados['password']) : '';

            if (empty($username)) {
                $this->view('dashboard/login/page', ['erro' => 'Usuário ou senha inválido!']);
                return;
            } elseif (empty($password)) {
                $this->view('dashboard/login/page', ['erro' => 'Usuário ou senha inválido!']);
                return;
            } else {
                // Autenticação
                $confirma_senha = Usuario::autentica_uauario($_POST['username'], $_POST['password']);

                if ($confirma_senha) {

                    $_SESSION['user'] = [
                        'id' => $confirma_senha['id'],
                        'nome' => $confirma_senha['nome'],
                        'username' => $confirma_senha['email'],
                    ];

                    // Redireciona para dashboard
                    header('Location: /dashboard');
                    exit;
                } else {
                    $this->view('dashboard/login/page', ['erro' => 'Usuário ou senha inválido!']);
                }
            }
        }
    }



    public function logout()
    {
        SessionSecurity::destruirSessao();
        header('Location: /dashboard/login');
        exit;
    }

    // public function verificar_senha()
    // {
    //     $cpf = $_POST['cpf'];
    //     $senha = $_POST['senha'];

    //     $candidato = Candidato::confirma_senha($cpf);

    //     if ($candidato && password_verify($senha, $candidato->senha_hash)) {
    //         // ✅ Usar login seguro
    //         SessionSecurity::criarLoginSeguroUsuario([
    //             'id' => $candidato->id,
    //             'nome' => $candidato->nome,
    //             'cpf' => $candidato->cpf,
    //         ]);

    //         header('Location: /painel');
    //         exit;
    //     } else {
    //         // Senha incorreta, redireciona de volta para a página de senha com uma mensagem de erro
    //         header('Location: /login');
    //     }
    // }
}
