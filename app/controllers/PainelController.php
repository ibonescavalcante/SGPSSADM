<?php

namespace App\controllers;

use App\models\Candidato;
use App\models\Inscricao;
use App\models\Pss;
use App\models\Naturalidade;


class PainelController extends Controller
{
    public function __construct()
    {
        // Chamar o construtor da classe pai para inicializar o Plates
        parent::__construct();
        
        // // Verificar se o usuário está logado
        // if (!isset($_SESSION['usuario'])) {
        //     header('Location: /login');
        //     exit;
        // }
    }

    public function index()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        try {
            $candidato_id = $_SESSION['usuario']['id'];
            
            // Buscar dados básicos do candidato
            $candidato = Candidato::buscarPorId($candidato_id);
            
            // Buscar inscrições do candidato
            $inscricoes = [];
            try {
                $inscricoes = Inscricao::buscarPorCandidato($candidato_id);
            } catch (\Exception $e) {
                error_log("Erro ao buscar inscrições: " . $e->getMessage());
            }
            
            // Buscar PSS disponíveis
            $pss_disponiveis = [];
            try {
                $pss_disponiveis = Pss::buscarAbertos();
            } catch (\Exception $e) {
                error_log("Erro ao buscar PSS: " . $e->getMessage());
            }

            $this->view('painel/painel', [
                'candidato' => $candidato,
                'inscricoes' => $inscricoes,
                'pss_disponiveis' => $pss_disponiveis
            ]);
        } catch (\Exception $e) {
            error_log("Erro no painel: " . $e->getMessage());
            echo "Erro interno. Tente novamente.";
        }
    }

    public function dados_usuario()
    {
        try {
            // Verificar se usuário está logado
            if (!isset($_SESSION['usuario'])) {
                header('Location: /login');
                exit;
            }
            
            $candidato_id = $_SESSION['usuario']['id'];
            $mensagem = '';
            $tipo_mensagem = '';
            
            // Processar formulário se for POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    // Verificar se pode editar dados antes de processar
                    require_once __DIR__ . '/../helpers/InscricaoValidacoes.php';
                    $validacao_edicao = \App\controllers\InscricaoValidacoes::validarEdicaoDadosPessoais($candidato_id);
                    
                    if (!$validacao_edicao['pode_editar']) {
                        $mensagem = $validacao_edicao['mensagem'];
                        $tipo_mensagem = 'erro';
                    } else {
                        // Validar dados básicos
                        $erros = [];
                        
                        if (empty(trim($_POST['nome'] ?? ''))) {
                            $erros[] = 'Nome é obrigatório';
                        }
                        
                        if (empty(trim($_POST['email'] ?? ''))) {
                            $erros[] = 'Email é obrigatório';
                        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                            $erros[] = 'Email inválido';
                        }
                        
                        if (empty($erros)) {
                            // Tentar atualizar dados com campos que existem na tabela
                            try {
                                $db = \App\core\Database::getInstance();
                                
                                // Preparar dados para atualização com validação de campos
                                $sql = "UPDATE pss.candidato SET 
                                            nome = :nome,
                                            email = :email,
                                            atualizado_em = now()";
                                
                                $params = [
                                    'nome' => trim($_POST['nome'] ?? ''),
                                    'email' => trim($_POST['email'] ?? ''),
                                    'id' => $candidato_id
                                ];
                                
                                // Adicionar campos opcionais apenas se foram enviados e não estão vazios
                                $camposOpcionais = [
                                    'nome_social' => 'nome_social',
                                    'data_nascimento' => 'data_nascimento', 
                                    'genero_id' => 'genero_id',
                                    'nome_mae' => 'nome_mae',
                                    'escolaridade_id' => 'escolaridade_id',
                                    'naturalidade_estado_id' => 'naturalidade_estado_id',
                                    'naturalidade_cidade_id' => 'naturalidade_cidade_id',
                                    'cep' => 'cep',
                                    'uf' => 'uf',
                                    'endereco' => 'endereco',
                                    'numero' => 'numero',
                                    'complemento' => 'complemento',
                                    'bairro' => 'bairro'
                                ];
                                
                                // ✅ CORREÇÃO: Adicionar campos municipio_id e estado_id
                                $camposOpcionais['municipio_id'] = 'municipio_id';
                                $camposOpcionais['estado_id'] = 'estado_id';
                                $camposOpcionais['municipio'] = 'municipio';
                                
                                foreach ($camposOpcionais as $campo => $coluna) {
                                    if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
                                        $sql .= ", $coluna = :$campo";
                                        
                                        // Validar campos numéricos
                                        if (in_array($campo, ['genero_id', 'escolaridade_id', 'naturalidade_estado_id', 'naturalidade_cidade_id', 'municipio_id', 'estado_id'])) {
                                            $params[$campo] = (int)$_POST[$campo];
                                        } else {
                                            $params[$campo] = trim($_POST[$campo]);
                                        }
                                    }
                                }
                                
                                $sql .= " WHERE id = :id";
                                
                                // Log para debug
                                error_log("SQL de atualização: " . $sql);
                                error_log("Parâmetros: " . json_encode($params));
                                
                                $stmt = $db->prepare($sql);
                                $resultado = $stmt->execute($params);
                                
                                if ($resultado) {
                                    // Verificar se realmente atualizou
                                    $linhas_afetadas = $stmt->rowCount();
                                    error_log("Linhas afetadas: " . $linhas_afetadas);
                                    
                                    $mensagem = 'Dados atualizados com sucesso!';
                                    $tipo_mensagem = 'sucesso';
                                    
                                    // Log dos campos municipio_id e estado_id para verificação
                                    if (isset($_POST['municipio_id']) && $_POST['municipio_id'] !== '') {
                                        error_log("municipio_id salvo: " . $_POST['municipio_id']);
                                    }
                                    if (isset($_POST['estado_id']) && $_POST['estado_id'] !== '') {
                                        error_log("estado_id salvo: " . $_POST['estado_id']);
                                    }
                                } else {
                                    $mensagem = 'Erro ao atualizar dados. Tente novamente.';
                                    $tipo_mensagem = 'erro';
                                }
                            } catch (\Exception $e) {
                                error_log("Erro ao atualizar dados: " . $e->getMessage());
                                $mensagem = 'Erro interno ao salvar: ' . $e->getMessage();
                                $tipo_mensagem = 'erro';
                            }
                        } else {
                            $mensagem = 'Corrija os erros: ' . implode(', ', $erros);
                            $tipo_mensagem = 'erro';
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Erro no processamento do formulário: " . $e->getMessage());
                    $mensagem = 'Erro interno. Tente novamente.';
                    $tipo_mensagem = 'erro';
                }
            }
            
            // Buscar dados atuais do candidato com relacionamentos
            $candidato = [];
            try {
                $db = \App\core\Database::getInstance();
                
                // ✅ CORREÇÃO: Incluir campos municipio_id e estado_id na consulta
                $sql = "SELECT c.*, 
                               g.nome as genero_nome,
                               e.escolaridade as escolaridade_nome,
                               est.estado as naturalidade_estado_nome,
                               est.sigla as naturalidade_estado_sigla,
                               n.cidade as naturalidade_cidade_nome,
                               est_end.estado as endereco_estado_nome,
                               est_end.sigla as endereco_estado_sigla,
                               mun.nome as endereco_municipio_nome
                        FROM pss.candidato c
                        LEFT JOIN pss.generos g ON c.genero_id = g.id
                        LEFT JOIN pss.escolaridade e ON c.escolaridade_id = e.id
                        LEFT JOIN pss.estados est ON c.naturalidade_estado_id = est.id
                        LEFT JOIN pss.naturalidade n ON c.naturalidade_cidade_id = n.id
                        LEFT JOIN pss.estados est_end ON c.estado_id = est_end.id
                        LEFT JOIN pss.municipios mun ON c.municipio_id = mun.id
                        WHERE c.id = :id";
                
                $stmt = $db->prepare($sql);
                $stmt->execute(['id' => $candidato_id]);
                $candidato = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if (!$candidato) {
                    $candidato = ['nome' => $_SESSION['usuario']['nome'] ?? 'Usuário'];
                }
                
                // Log para debug dos campos de endereço
                error_log("Dados do candidato - municipio_id: " . ($candidato['municipio_id'] ?? 'null') . ", estado_id: " . ($candidato['estado_id'] ?? 'null'));
                
            } catch (\Exception $e) {
                error_log("Erro ao buscar dados do candidato: " . $e->getMessage());
                $candidato = ['nome' => $_SESSION['usuario']['nome'] ?? 'Usuário'];
            }
            
            // Buscar dados para os selects
            $generos = [];
            $escolaridades = [];
            $estados = [];
            
            try {
                $db = \App\core\Database::getInstance();
                
                // Buscar gêneros
                try {
                    $stmt = $db->prepare("SELECT id, nome FROM pss.generos ORDER BY nome");
                    $stmt->execute();
                    $generos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } catch (\Exception $e) {
                    error_log("Erro ao buscar gêneros: " . $e->getMessage());
                }
                
                // Buscar escolaridades
                try {
                    $stmt = $db->prepare("SELECT id, escolaridade FROM pss.escolaridade ORDER BY id");
                    $stmt->execute();
                    $escolaridades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } catch (\Exception $e) {
                    error_log("Erro ao buscar escolaridades: " . $e->getMessage());
                }
                
                // Buscar estados
                try {
                    $stmt = $db->prepare("SELECT id, sigla, estado FROM pss.estados ORDER BY estado");
                    $stmt->execute();
                    $estados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } catch (\Exception $e) {
                    error_log("Erro ao buscar estados: " . $e->getMessage());
                }
                
            } catch (\Exception $e) {
                error_log("Erro ao conectar com banco: " . $e->getMessage());
            }
            
            // Verificar se pode editar dados
            require_once __DIR__ . '/../helpers/InscricaoValidacoes.php';
            $validacao_edicao = \App\controllers\InscricaoValidacoes::validarEdicaoDadosPessoais($candidato_id);
            $pode_editar = $validacao_edicao['pode_editar'];
            $mensagem_restricao = $validacao_edicao['pode_editar'] ? '' : $validacao_edicao['mensagem'];
            
            $this->view("painel/painel-dados-usuario", [
                "candidato" => $candidato,
                "generos" => $generos,
                "escolaridades" => $escolaridades,
                "estados" => $estados,
                "pode_editar" => $pode_editar,
                "mensagem" => $mensagem,
                "tipo_mensagem" => $tipo_mensagem,
                "mensagem_restricao" => $mensagem_restricao ?? ''
            ]);
            
        } catch (\Exception $e) {
            error_log("Erro geral em dados_usuario: " . $e->getMessage());
            echo "Erro interno. Tente novamente mais tarde.";
        }
    }
    
    /**
     * ✅ CORREÇÃO: API melhorada para buscar cidades por estado (AJAX)
     */
    public function buscar_cidades_por_estado($id)
    {
        header("Content-Type: application/json");
        
        // if (!isset($_SESSION["usuario"])) {
        //     http_response_code(401);
        //     echo json_encode(["erro" => "Não autorizado"]);
        //     return;
        // }
        
        try {
            $cidades = Naturalidade::buscarPorEstado($id);
            // $db = \App\core\Database::getInstance();
            
            // // Tentar diferentes tabelas de municípios/cidades
            // $queries = [
            //     // Primeira tentativa: tabela municipios
            //     "SELECT id, nome as cidade FROM pss.municipios WHERE estado_id = :estado_id ORDER BY nome",
            //     // Segunda tentativa: tabela cidades
            //     "SELECT id, nome as cidade FROM pss.cidades WHERE estado_id = :estado_id ORDER BY nome",
            //     // Terceira tentativa: tabela naturalidade
            //     "SELECT id, cidade FROM pss.naturalidade WHERE estado_id = :estado_id ORDER BY cidade"
            // ];
            
            // $cidades = [];
            
            // foreach ($queries as $sql) {
            //     try {
            //         $stmt = $db->prepare($sql);
            //         $stmt->execute(["estado_id" => $id]);
            //         $cidades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
            //         if (!empty($cidades)) {
            //             error_log("Cidades encontradas na consulta: " . $sql);
            //             break;
            //         }
            //     } catch (\Exception $e) {
            //         error_log("Erro na consulta: " . $sql . " - " . $e->getMessage());
            //         continue;
            //     }
            // }
            
            // // Log para debug
            // error_log("Total de cidades encontradas para estado_id $id: " . count($cidades));
            
             echo json_encode($cidades);
        } catch (\Exception $e) {
            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }

    public function alterar_senha()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $candidato_id = $_SESSION['usuario']['id'];
                $candidato = Candidato::buscarPorId($candidato_id);

                $senha_atual = $_POST['senha_atual'] ?? '';
                $nova_senha = $_POST['nova_senha'] ?? '';
                $confirmar_senha = $_POST['confirmar_senha'] ?? '';

                // Verificar senha atual
                if (!password_verify($senha_atual, $candidato['senha_hash'])) {
                    $erro = 'Senha atual incorreta';
                } elseif ($nova_senha !== $confirmar_senha) {
                    $erro = 'Nova senha e confirmação não coincidem';
                } elseif (strlen($nova_senha) < 6) {
                    $erro = 'Nova senha deve ter pelo menos 6 caracteres';
                } else {
                    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    
                    try {
                        $db = \App\core\Database::getInstance();
                        $stmt = $db->prepare("UPDATE pss.candidato SET senha_hash = :senha WHERE id = :id");
                        $resultado = $stmt->execute(['senha' => $nova_senha_hash, 'id' => $candidato_id]);
                        
                        if ($resultado) {
                            header('Location: /painel?success=Senha alterada com sucesso');
                            exit;
                        } else {
                            $erro = 'Erro ao alterar senha';
                        }
                    } catch (\Exception $e) {
                        error_log("Erro ao alterar senha: " . $e->getMessage());
                        $erro = 'Erro interno ao alterar senha';
                    }
                }
            }

            $this->view('painel/painel-alterar-senha', [
                'erro' => $erro ?? null
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao alterar senha: " . $e->getMessage());
            echo "Erro interno. Tente novamente.";
        }
    }

    public function inscricoes()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        try {
            $candidato_id = $_SESSION['usuario']['id'];
            $inscricoes = Inscricao::buscarPorCandidato($candidato_id);

            $this->view('painel/painel-inscricao', [
                'inscricoes' => $inscricoes
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar inscrições: " . $e->getMessage());
            echo "Erro interno. Tente novamente.";
        }
    }

    /**
     * Cancela uma inscrição do candidato
     */
    public function cancelarInscricao($inscricao_id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /painel');
                exit;
            }

            $candidato_id = $_SESSION['usuario']['id'];
            
            // Verificar se a inscrição pertence ao candidato
            $inscricao = Inscricao::buscarPorId($inscricao_id);
            
            if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
                header('Location: /painel?erro=Inscrição não encontrada');
                exit;
            }
            
            // Verificar se pode cancelar (período de inscrição ainda aberto)
            $pss = Pss::buscarPorId($inscricao['pss_id']);
            $agora = new \DateTime();
            $fim_inscricao = new \DateTime($pss['inscricao_fim']);
            
            if ($agora > $fim_inscricao) {
                header('Location: /painel?erro=Não é possível cancelar após o fim das inscrições');
                exit;
            }
            
            // Cancelar inscrição
            if (Inscricao::cancelar($inscricao_id)) {
                header('Location: /painel?success=Inscrição cancelada com sucesso');
            } else {
                header('Location: /painel?erro=Erro ao cancelar inscrição');
            }
            exit;
            
        } catch (\Exception $e) {
            error_log("Erro ao cancelar inscrição: " . $e->getMessage());
            header('Location: /painel?erro=Erro interno');
            exit;
        }
    }

    public function recurso()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        try {
            $candidato_id = $_SESSION['usuario']['id'];
            
            // Buscar inscrições do candidato para o formulário
            $inscricoes = Inscricao::buscarPorCandidato($candidato_id);
            
            // Buscar etapas disponíveis para recurso
            $etapas = \App\models\Recurso::buscarEtapasDisponiveis();
            
            // Buscar recursos já enviados
            $recursos = \App\models\Recurso::buscarPorCandidato($candidato_id);

            $this->view('painel/painel-recurso', [
                'inscricoes' => $inscricoes,
                'etapas' => $etapas,
                'recursos' => $recursos
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao carregar página de recurso: " . $e->getMessage());
            echo "Erro interno. Tente novamente.";
        }
    }

    public function enviarRecurso()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /painel/recurso');
                exit;
            }

            $candidato_id = $_SESSION['usuario']['id'];
            $inscricao_id = $_POST['inscricao_id'] ?? '';
            $etapa_id = $_POST['etapa_id'] ?? '';
            $conteudo = trim($_POST['conteudo'] ?? '');

            // Validações
            if (!$inscricao_id || !$etapa_id || empty($conteudo)) {
                header('Location: /painel/recurso?erro=Todos os campos são obrigatórios');
                exit;
            }
            
            if (strlen($conteudo) > 2000) {
                header('Location: /painel/recurso?erro=O conteúdo do recurso deve ter no máximo 2000 caracteres');
                exit;
            }
            
            // Verificar se a inscrição pertence ao candidato
            $inscricao = Inscricao::buscarPorId($inscricao_id);
            if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
                header('Location: /painel/recurso?erro=Inscrição não encontrada');
                exit;
            }
            
            // Verificar se a etapa permite recurso e está no prazo
            if (!\App\models\Recurso::verificarPrazoRecurso($etapa_id)) {
                header('Location: /painel/recurso?erro=Prazo para recurso encerrado ou etapa não permite recurso');
                exit;
            }
            
            // Verificar se já existe recurso
            $recurso_existente = \App\models\Recurso::verificarRecursoExistente($inscricao_id, $etapa_id);
            if ($recurso_existente) {
                header('Location: /painel/recurso?erro=Você já possui um recurso para esta etapa');
                exit;
            }
            
            // Criar o recurso
            $dados = [
                'inscricao_id' => $inscricao_id,
                'etapa_id' => $etapa_id,
                'conteudo' => $conteudo,
                'status' => 'pendente',
                'dt_abertura' => date('Y-m-d H:i:s'),
                'dt_decisao' => null,
                'motivo_decisao' => null,
                'avaliador_id' => null
            ];
            
            if (\App\models\Recurso::criar($dados)) {
                header("Location: /painel/recurso?success=Recurso enviado com sucesso!");
                exit;
            } else {
                header('Location: /painel/recurso?erro=Erro ao enviar recurso. Tente novamente.');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erro ao enviar recurso: " . $e->getMessage());
            header('Location: /painel/recurso?erro=Erro interno');
            exit;
        }
    }
}
