<?php

namespace App\controllers;

use App\models\Pss;
use App\models\PssCargo;

class AdminPssController extends Controller
{
    public function __construct()
    {
        // Chamar o construtor da classe pai para inicializar o Plates
        parent::__construct();
        
        // Verificar se é administrador
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function index()
    {
        try {
            $pss_list = Pss::buscarTodos();
            
            $this->view('admin/pss/listar', [
                'pss_list' => $pss_list
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao buscar PSS: " . $e->getMessage());
            $this->view('admin/pss/listar', [
                'pss_list' => [],
                'erro' => 'Erro ao carregar lista de PSS'
            ]);
        }
    }

    public function criar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'titulo' => $_POST['titulo'],
                'secretaria' => $_POST['secretaria'],
                'ano_exercicio' => $_POST['ano_exercicio'],
                'descricao' => $_POST['descricao'],
                'inscricao_ini' => $_POST['inscricao_ini'],
                'inscricao_fim' => $_POST['inscricao_fim'],
                'status_global' => $_POST['status_global'] ?? 'em_andamento',
                'metas_json' => json_encode([
                    'documentos_obrigatorios' => $_POST['documentos_obrigatorios'] ?? [],
                    'prazos' => [
                        'inscricao' => [
                            'inicio' => $_POST['inscricao_ini'],
                            'fim' => $_POST['inscricao_fim']
                        ],
                        'analise_documentos' => [
                            'inicio' => $_POST['analise_ini'] ?? null,
                            'fim' => $_POST['analise_fim'] ?? null
                        ],
                        'recursos' => [
                            'inicio' => $_POST['recursos_ini'] ?? null,
                            'fim' => $_POST['recursos_fim'] ?? null
                        ],
                        'resultado_final' => $_POST['resultado_final'] ?? null
                    ],
                    'configuracoes' => [
                        'permite_nome_social' => isset($_POST['permite_nome_social']),
                        'exige_comprovante_residencia' => isset($_POST['exige_comprovante_residencia']),
                        'permite_recursos' => isset($_POST['permite_recursos']),
                        'permite_impugnacao' => isset($_POST['permite_impugnacao'])
                    ]
                ])
            ];

            if (Pss::criar($dados)) {
                header('Location: /admin/pss?success=PSS criado com sucesso');
                exit;
            } else {
                $erro = 'Erro ao criar PSS';
            }
        }

        $this->view('admin/pss/criar', [
            'erro' => $erro ?? null
        ]);
    }

    public function editar($id)
    {
        $pss = Pss::buscarPorId($id);
        if (!$pss) {
            header('Location: /admin/pss?erro=PSS não encontrado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'id' => $id,
                'titulo' => $_POST['titulo'],
                'secretaria' => $_POST['secretaria'],
                'ano_exercicio' => $_POST['ano_exercicio'],
                'descricao' => $_POST['descricao'],
                'inscricao_ini' => $_POST['inscricao_ini'],
                'inscricao_fim' => $_POST['inscricao_fim'],
                'status_global' => $_POST['status_global'],
                'metas_json' => json_encode([
                    'documentos_obrigatorios' => $_POST['documentos_obrigatorios'] ?? [],
                    'prazos' => [
                        'inscricao' => [
                            'inicio' => $_POST['inscricao_ini'],
                            'fim' => $_POST['inscricao_fim']
                        ],
                        'analise_documentos' => [
                            'inicio' => $_POST['analise_ini'] ?? null,
                            'fim' => $_POST['analise_fim'] ?? null
                        ],
                        'recursos' => [
                            'inicio' => $_POST['recursos_ini'] ?? null,
                            'fim' => $_POST['recursos_fim'] ?? null
                        ],
                        'resultado_final' => $_POST['resultado_final'] ?? null
                    ],
                    'configuracoes' => [
                        'permite_nome_social' => isset($_POST['permite_nome_social']),
                        'exige_comprovante_residencia' => isset($_POST['exige_comprovante_residencia']),
                        'permite_recursos' => isset($_POST['permite_recursos']),
                        'permite_impugnacao' => isset($_POST['permite_impugnacao'])
                    ]
                ])
            ];

            if (Pss::atualizar($dados)) {
                header('Location: /admin/pss?success=PSS atualizado com sucesso');
                exit;
            } else {
                $erro = 'Erro ao atualizar PSS';
            }
        }

        $this->view('admin/pss/editar', [
            'pss' => $pss,
            'erro' => $erro ?? null
        ]);
    }

    public function gerenciarCargos($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header('Location: /admin/pss?erro=PSS não encontrado');
            exit;
        }

        $cargos = PssCargo::buscarPorPss($pss_id);

        $this->view('admin/pss/cargos', [
            'pss' => $pss,
            'cargos' => $cargos
        ]);
    }

    public function criarCargo($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header('Location: /admin/pss?erro=PSS não encontrado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'pss_id' => $pss_id,
                'nome' => $_POST['nome'],
                'zona' => $_POST['zona'],
                'vagas_total' => $_POST['vagas_total'],
                'vagas_pcd' => $_POST['vagas_pcd'] ?? 0,
                'vagas_ppp' => $_POST['vagas_ppp'] ?? 0,
                'salario' => $_POST['salario'],
                'requisitos' => $_POST['requisitos'],
                'atribuicoes' => $_POST['atribuicoes'] ?? null,
                'documentos_especificos' => $_POST['documentos_especificos'] ?? null,
                'cadastro_reserva' => isset($_POST['cadastro_reserva'])
            ];

            if (PssCargo::criar($dados)) {
                header('Location: /admin/pss/' . $pss_id . '/cargos?success=Cargo criado com sucesso');
                exit;
            } else {
                $erro = 'Erro ao criar cargo';
            }
        }

        $this->view('admin/pss/criar_cargo', [
            'pss' => $pss,
            'erro' => $erro ?? null
        ]);
    }

    public function visualizar($id)
    {
        try {
            $pss = Pss::buscarPorId($id);
            
            if (!$pss) {
                $this->view('admin/pss/listar', [
                    'pss_list' => [],
                    'erro' => 'PSS não encontrado'
                ]);
                return;
            }
            
            $this->view('admin/pss/visualizar', [
                'pss' => $pss
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao visualizar PSS: " . $e->getMessage());
            $this->view('admin/pss/listar', [
                'pss_list' => [],
                'erro' => 'Erro ao carregar PSS'
            ]);
        }
    }

    public function inativar($id)
    {
        try {
            $pss = Pss::buscarPorId($id);
            
            if (!$pss) {
                header('Location: /admin/pss?erro=PSS não encontrado');
                exit;
            }
            
            // Atualizar status para inativo
            $resultado = Pss::atualizar($id, [
                'titulo' => $pss['titulo'],
                'secretaria' => $pss['secretaria'],
                'ano_exercicio' => $pss['ano_exercicio'],
                'descricao' => $pss['descricao'],
                'status_global' => 'inativo',
                'inscricao_ini' => $pss['inscricao_ini'],
                'inscricao_fim' => $pss['inscricao_fim'],
                'metas_json' => $pss['metas_json']
            ]);
            
            if ($resultado) {
                header('Location: /admin/pss?sucesso=PSS inativado com sucesso');
            } else {
                header('Location: /admin/pss?erro=Erro ao inativar PSS');
            }
            exit;
            
        } catch (\Exception $e) {
            error_log("Erro ao inativar PSS: " . $e->getMessage());
            header('Location: /admin/pss?erro=Erro ao inativar PSS');
            exit;
        }
    }
}

