<?php

namespace App\controllers;

use App\models\Candidato;

class CadastrarController extends Controller
{
    public function index()
    {
        try {
            // Validação de campos obrigatórios
            $camposObrigatorios = [
                'nome' => 'Nome completo',
                'cpf' => 'CPF',
                'data_nascimento' => 'Data de nascimento',
                'genero_id' => 'Gênero',
                'email' => 'Email',
                'senha' => 'Senha',
                'bairro' => 'Bairro',
            ];

            $erros = [];
            foreach ($camposObrigatorios as $campo => $nome) {
                if (empty($_POST[$campo])) {
                    $erros[] = "O campo '{$nome}' é obrigatório.";
                }
            }

            // Validação de confirmação de senha
            if (!empty($_POST['senha']) && !empty($_POST['confirmar_senha'])) {
                if ($_POST['senha'] !== $_POST['confirmar_senha']) {
                    $erros[] = "As senhas não coincidem.";
                }
            }

            // Validação de CPF único
            if (!empty($_POST['cpf'])) {
                $cpfLimpo = preg_replace('/[^0-9]/', '', $_POST['cpf']);
                if (Candidato::buscarPorCpf($cpfLimpo)) {
                    $erros[] = "CPF já cadastrado no sistema.";
                }
            }

            // Validação de email único
            if (!empty($_POST['email'])) {
                if (Candidato::buscarPorEmail($_POST['email'])) {
                    $erros[] = "Email já cadastrado no sistema.";
                }
            }

            // Se há erros, retornar
            if (!empty($erros)) {
                throw new Exception(implode(' ', $erros));
            }

            // Mapeamento dos dados
            $dados = [];
            
            // Campos básicos obrigatórios
            $dados['cpf'] = preg_replace('/[^0-9]/', '', $_POST['cpf']);
            $dados['nome'] = trim($_POST['nome']);
            $dados['email'] = trim($_POST['email']);
            $dados['data_nascimento'] = $_POST['data_nascimento'];
            $dados['genero_id'] = (int)$_POST['genero_id'];
            $dados['bairro'] = trim($_POST['bairro']);
            $dados['celular'] = isset($_POST['celular']) ? trim($_POST['celular']) : null;
            
            // ✅ CORREÇÃO: Campos de endereço completos
            $dados['endereco'] = isset($_POST['endereco']) && !empty($_POST['endereco']) ? trim($_POST['endereco']) : null;
            $dados['numero'] = isset($_POST['numero']) && !empty($_POST['numero']) ? trim($_POST['numero']) : null;
            $dados['complemento'] = isset($_POST['complemento']) && !empty($_POST['complemento']) ? trim($_POST['complemento']) : null;
            $dados['cep'] = isset($_POST['cep']) && !empty($_POST['cep']) ? preg_replace('/[^0-9]/', '', $_POST['cep']) : null;
            
            // ✅ CORREÇÃO: Campos de estado e município do endereço
            $dados["municipio_id"] = isset($_POST["municipio_id"]) && !empty($_POST["municipio_id"]) ? (int)$_POST["municipio_id"] : null;
            $dados["estado_id"] = isset($_POST["estado_id"]) && !empty($_POST["estado_id"]) ? (int)$_POST["estado_id"] : null;
            
            // ✅ CORREÇÃO: Campo municipio (nome da cidade) - pode vir do select ou do campo naturalidade_cidade_id
            if (isset($_POST['naturalidade_cidade_id']) && !empty($_POST['naturalidade_cidade_id'])) {
                // Se foi selecionada uma cidade de naturalidade, usar como município também
                $dados["municipio_id"] = (int)$_POST['naturalidade_cidade_id'];
                
                // Buscar o nome da cidade para salvar no campo municipio
                try {
                    $db = \App\core\Database::getInstance();
                    $stmt = $db->prepare("SELECT cidade FROM pss.naturalidade WHERE id = :id LIMIT 1");
                    $stmt->execute(['id' => $_POST['naturalidade_cidade_id']]);
                    $cidade = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($cidade) {
                        $dados['municipio'] = $cidade['cidade'];
                    }
                } catch (\Exception $e) {
                    error_log("Erro ao buscar nome da cidade: " . $e->getMessage());
                }
            }
            
            // Outros campos opcionais
            $dados['acessibilidade'] = false;
            $dados['escolaridade_id'] = isset($_POST['escolaridade_id']) && !empty($_POST['escolaridade_id']) ? (int)$_POST['escolaridade_id'] : null;
            $dados['pcd'] = isset($_POST['pcd']) && $_POST['pcd'] === 'sim';
            $dados['ppp'] = isset($_POST['ppp']) && $_POST['ppp'] === 'sim';
            $dados['senha_hash'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            $dados['nome_social'] = isset($_POST['nome_social']) && !empty($_POST['nome_social']) ? trim($_POST['nome_social']) : null;
            $dados['aceita_lgpd'] = isset($_POST['aceita_lgpd']) ? (bool)$_POST['aceita_lgpd'] : false;
            $dados['data_aceite_lgpd'] = $dados['aceita_lgpd'] ? date('Y-m-d H:i:s') : null;
            $dados['aceita_publicidade'] = isset($_POST['aceita_publicidade']) ? (bool)$_POST['aceita_publicidade'] : false;
            
            // Documento (se enviados)
            if (isset($_POST['documento_tipo'])) {
                $dados['documento_tipo'] = $_POST['documento_tipo'];
            }
            if (isset($_POST['documento_numero'])) {
                $dados['documento_numero'] = !empty($_POST['documento_numero']) ? trim($_POST['documento_numero']) : null;
            }
            if (isset($_POST['documento_orgao'])) {
                $dados['documento_orgao'] = !empty($_POST['documento_orgao']) ? trim($_POST['documento_orgao']) : null;
            }
            if (isset($_POST['documento_uf_id'])) {
                $dados['documento_uf_id'] = !empty($_POST['documento_uf_id']) ? (int)$_POST['documento_uf_id'] : null;
            }
            
            // ✅ CORREÇÃO: Naturalidade (campos corrigidos)
            if (isset($_POST['nacionalidade_id'])) {
                $dados['nacionalidade_id'] = !empty($_POST['nacionalidade_id']) ? (int)$_POST['nacionalidade_id'] : null;
            }
            if (isset($_POST['naturalidade_estado_id'])) {
                $dados['naturalidade_estado_id'] = !empty($_POST['naturalidade_estado_id']) ? (int)$_POST['naturalidade_estado_id'] : null;
            }
            if (isset($_POST['naturalidade_cidade_id'])) {
                $dados['naturalidade_cidade_id'] = !empty($_POST['naturalidade_cidade_id']) ? (int)$_POST['naturalidade_cidade_id'] : null;
            }
            
            // Outros (se enviados)
            if (isset($_POST['nome_mae'])) {
                $dados['nome_mae'] = !empty($_POST['nome_mae']) ? trim($_POST['nome_mae']) : null;
            }

            // ✅ CORREÇÃO: Log dos dados para debug
            error_log("Dados do candidato para cadastro: " . json_encode($dados));

            // Cadastrar candidato
            $retorno = Candidato::cadastrar($dados);

            if ($retorno) {
                $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Você já pode fazer login.";
                header("Location: /login");
                exit;
            } else {
                throw new Exception("Erro interno ao salvar os dados. Tente novamente.");
            }

        } catch (Exception $e) {
            error_log("Erro no cadastro de candidato: " . $e->getMessage());
            $_SESSION['erro_cadastro'] = $e->getMessage();
            $_SESSION['dados_formulario'] = $_POST;
            header("Location: /cadastro");
            exit;
        } catch (PDOException $e) {
            error_log("Erro PDO no cadastro: " . $e->getMessage());
            $_SESSION['erro_cadastro'] = "Erro de conexão com o banco de dados. Tente novamente mais tarde.";
            $_SESSION['dados_formulario'] = $_POST;
            header("Location: /cadastro");
            exit;
        }
    }
}
