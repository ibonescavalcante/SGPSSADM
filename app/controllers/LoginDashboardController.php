<?php

namespace App\controllers;

use App\models\Usuario;
use App\models\UsuarioSessaoDashboard;
use App\middleware\SessionSecurity;

use App\core\Controller;
class LoginDashboardController extends Controller
{
    public function index()
    {
        if (SessionSecurity::estaLogadoDashboard()) {
            header('Location: /dashboard');
            exit;
        }
        $data = [];
        if (isset($_GET['sessao']) && $_GET['sessao'] === 'substituida') {
            $data['erro'] = 'Sessão encerrada. Esta conta foi acessada em outro dispositivo ou navegador.';
        }
        $this->view('dashboard/login/page', $data);
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
                    try {
                        if (session_status() === PHP_SESSION_ACTIVE) {
                            session_regenerate_id(true);
                        }

                        $token = bin2hex(random_bytes(32));
                        UsuarioSessaoDashboard::registrarOuAtualizar((int) $confirma_senha['id'], $token);

                        $perfilSessao = strtolower(trim((string) ($confirma_senha['perfil'] ?? '')));

                        $_SESSION['user'] = [
                            'id' => $confirma_senha['id'],
                            'nome' => $confirma_senha['nome'],
                            'username' => $confirma_senha['email'],
                            'perfil' => $perfilSessao,
                            'dashboard_sessao_token' => $token,
                        ];
                        SessionSecurity::regenerateDashboardCsrfToken();

                        header('Location: /dashboard');
                        exit;
                    } catch (\Throwable $e) {
                        error_log('Login dashboard sessão: ' . $e->getMessage());
                        $this->view('dashboard/login/page', [
                            'erro' => 'Não foi possível concluir o login. Tente novamente ou contate o suporte.',
                        ]);
                        return;
                    }
                } else {
                    $this->view('dashboard/login/page', ['erro' => 'Usuário ou senha inválido!']);
                }
            }
        }
    }



    public function logout()
    {
        if (SessionSecurity::estaLogadoDashboard() && SessionSecurity::validarVinculoSessaoDashboard()) {
            try {
                UsuarioSessaoDashboard::removerPorUsuario((int) $_SESSION['user']['id']);
            } catch (\Throwable $e) {
                error_log('Logout remover sessão dashboard: ' . $e->getMessage());
            }
        }
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
