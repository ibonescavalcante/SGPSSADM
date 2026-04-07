<?php

namespace App\controllers;

use App\models\Candidato;
use App\models\Estado;
use App\models\Escolaridade;
use App\models\Genero;
use App\models\Nacionalidade;
use App\models\Naturalidade;
use App\middleware\SessionSecurity;

class LoginController extends Controller
{
    public function index()
    {
        $this->view('login/login');
    }

    public function logar()
    {
        if (empty($_POST['cpf'])) {
            $this->view('login/login');
            return;
        }

        if (!isValidaCPF($_POST['cpf'])) {
            $this->view('login/login', ['erro' => 'CPF inválido.']);
            return;
        }

        $buscarCandidato = Candidato::busca_CPF($_POST['cpf']);

        if ($buscarCandidato) {
            // CPF encontrado, redireciona para a página de senha
            $this->view('login/senha', ['cpf' => $_POST['cpf']]);
        } else {
            // ✅ CORREÇÃO: CPF não encontrado, redireciona para /cadastro
            $_SESSION['cpf_cadastro'] = $_POST['cpf'];
            $_SESSION['mensagem_cadastro'] = 'CPF não encontrado. Complete seu cadastro para continuar.';
            header('Location: /cadastro');
            exit;
        }
    }

    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validar dados obrigatórios
            $campos_obrigatorios = ["nome", "cpf", "data_nascimento", "genero_id", "email", "senha"];
            foreach ($campos_obrigatorios as $campo) {
                if (empty($_POST[$campo])) {
                    $_SESSION['erro_cadastro'] = "Campo {$campo} é obrigatório";
                    $_SESSION['dados_formulario'] = $_POST;
                    header('Location: /cadastro');
                    exit;
                }
            }

            // Verificar se CPF já existe
            if (Candidato::busca_CPF($_POST["cpf"])) {
                $_SESSION['erro_cadastro'] = "CPF já cadastrado";
                $_SESSION['dados_formulario'] = $_POST;
                header('Location: /cadastro');
                exit;
            }

            // Verificar se email já existe
            if (Candidato::buscarPorEmail($_POST["email"])) {
                $_SESSION['erro_cadastro'] = "Email já cadastrado";
                $_SESSION['dados_formulario'] = $_POST;
                header('Location: /cadastro');
                exit;
            }

            $data_nascimento_obj = \DateTime::createFromFormat('d/m/Y', $_POST["data_nascimento"]);
            if (!$data_nascimento_obj) {
                $_SESSION['erro_cadastro'] = "Formato de data de nascimento inválido. Use dd/mm/yyyy.";
                $_SESSION['dados_formulario'] = $_POST;
                header('Location: /cadastro');
                exit;
            }
            $data_nascimento_formatada = $data_nascimento_obj->format('Y-m-d');

            $dados = [
                "nome" => $_POST["nome"],
                "nome_social" => $_POST["nome_social"] ?? null,
                "cpf" => $_POST["cpf"],
                "data_nascimento" => $data_nascimento_formatada,
                "genero_id" => $_POST["genero_id"],
                "email" => $_POST["email"],
                "telefone" => $_POST["telefone"] ?? null,
                "endereco" => $_POST["endereco"] ?? null,
                "senha_hash" => password_hash($_POST["senha"], PASSWORD_DEFAULT),
                "documento_tipo" => $_POST["documento_tipo"] ?? "RG",
                "documento_numero" => $_POST["documento_numero"] ?? null,
                "documento_orgao" => $_POST["documento_orgao"] ?? null,
                "documento_uf_id" => $_POST["documento_uf_id"] ?? null,
                "escolaridade_id" => $_POST["escolaridade_id"] ?? null,
                "nome_mae" => $_POST["nome_mae"] ?? null,
                "nacionalidade_id" => $_POST["nacionalidade_id"] ?? null,
                "naturalidade_estado_id" => $_POST["naturalidade_estado_id"] ?? null,
                "naturalidade_cidade_id" => $_POST["naturalidade_cidade_id"] ?? null,
                "cep" => $_POST["cep"] ?? null,
                "numero" => $_POST["numero"] ?? null,
                "complemento" => $_POST["complemento"] ?? null,
                "bairro" => $_POST["bairro"] ?? null,
                "celular" => $_POST["celular"] ?? null,
                // ✅ CORREÇÃO: Campos de endereço separados
                "estado_id" => isset($_POST["estado_id"]) && !empty($_POST["estado_id"]) ? (int)$_POST["estado_id"] : null,
                "municipio_id" => isset($_POST["municipio_id"]) && !empty($_POST["municipio_id"]) ? (int)$_POST["municipio_id"] : null,
                "pcd" => isset($_POST["pcd"]) && $_POST["pcd"] === 'sim' ? true : false,
                "ppp" => isset($_POST["ppp"]) && $_POST["ppp"] === 'sim' ? true : false
            ];

            if (Candidato::cadastrar($dados)) {
                // Login automático após cadastro com segurança
                $candidato = Candidato::buscarPorCpf($_POST["cpf"]);
                SessionSecurity::criarLoginSeguroUsuario([
                    "id" => $candidato["id"],
                    "nome" => $candidato["nome"],
                    "cpf" => $candidato["cpf"],
                ]);
                
                // Limpar dados da sessão
                unset($_SESSION['cpf_cadastro']);
                unset($_SESSION['mensagem_cadastro']);
                unset($_SESSION['dados_formulario']);
                
                // Verificar se há dados de inscrição na sessão
                if (isset($_SESSION['inscricao_pendente'])) {
                    $pss_id = $_SESSION['inscricao_pendente']['pss_id'];
                    $cargo_id = $_SESSION['inscricao_pendente']['cargo_id'];
                    
                    // Finalizar inscrição
                    $protocolo = 'PSS' . date('Y') . str_pad($pss_id, 3, '0', STR_PAD_LEFT) . 
                                str_pad($cargo_id, 3, '0', STR_PAD_LEFT) . 
                                str_pad($candidato["id"], 6, '0', STR_PAD_LEFT);
                    
                    $inscricao_data = [
                        'pss_id' => $pss_id,
                        'pss_cargo_id' => $cargo_id,
                        'candidato_id' => $candidato["id"],
                        'status' => 'confirmada',
                        'protocolo' => $protocolo,
                        'dt_inscricao' => date('Y-m-d H:i:s'),
                        'extra_json' => json_encode([])
                    ];
                    
                    if (Inscricao::criar($inscricao_data)) {
                        // Limpar dados da sessão
                        unset($_SESSION['inscricao_pendente']);
                        
                        // Redirecionar para comprovante
                        header("Location: /inscricao/comprovante?success=Inscrição realizada com sucesso!");
                        exit;
                    }
                }
                
                header("Location: /painel?success=Cadastro realizado com sucesso!");
                exit;
            } else {
                $_SESSION['erro_cadastro'] = "Erro ao realizar cadastro";
                $_SESSION['dados_formulario'] = $_POST;
                header('Location: /cadastro');
                exit;
            }
        }
    }

    public function showCadastroForm()
    {
        $buscarEscolaridade = Escolaridade::buscarEscolaridade();
        $buscarGeneros = Genero::buscarGeneros();
        $buscarEstados = Estado::buscarUF();
        $buscarNacionalidades = Nacionalidade::buscarNacionalidade();
        $buscarNaturalidade = Naturalidade::buscarNaturalidade(14); // Pará

        $this->view(
            "inscricao/cadastro",
            [
                "estados" => $buscarEstados,
                "escolaridades" => $buscarEscolaridade,
                "generos" => $buscarGeneros,
                "nacionalidades" => $buscarNacionalidades,
                "naturalidade" => $buscarNaturalidade
            ]
        );
    }

    public function logout()
    {
        SessionSecurity::destruirSessao();
        header('Location: /login');
        exit;
    }

    public function verificar_senha()
    {
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        $candidato = Candidato::confirma_senha($cpf);

        if ($candidato && password_verify($senha, $candidato->senha_hash)) {
            // ✅ Usar login seguro
            SessionSecurity::criarLoginSeguroUsuario([
                'id' => $candidato->id,
                'nome' => $candidato->nome,
                'cpf' => $candidato->cpf,
            ]);
            
            header('Location: /painel');
            exit;
        } else {
            // Senha incorreta, redireciona de volta para a página de senha com uma mensagem de erro
            header('Location: /login');
        }
    }
}
