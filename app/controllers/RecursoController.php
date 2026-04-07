<?php

namespace App\controllers;

use App\models\Recurso;
use App\models\Inscricao;
use App\models\PssEtapa;
use App\models\Pss;

class RecursoController extends Controller
{
    public function meus_recursos()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        $recursos = Recurso::buscarPorCandidato($candidato_id);

        $this->view('candidato/meus_recursos', [
            'recursos' => $recursos
        ]);
    }

    public function responder()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $recurso_id = $input['recurso_id'] ?? null;
        $resposta = trim($input['resposta'] ?? '');

        if (!$recurso_id || empty($resposta)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            exit;
        }

        // Validar tamanho da resposta
        if (strlen($resposta) > 2000) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'A resposta não pode ter mais de 2.000 caracteres']);
            exit;
        }

        if (strlen($resposta) < 10) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'A resposta deve ter pelo menos 10 caracteres']);
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        
        // Verificar se o recurso pertence ao candidato
        $recurso = Recurso::buscarPorId($recurso_id);
        if (!$recurso) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['success' => false, 'message' => 'Recurso não encontrado']);
            exit;
        }

        // Verificar se o recurso pertence ao candidato logado
        $inscricao = Inscricao::buscarPorId($recurso['inscricao_id']);
        if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        // Verificar se o recurso ainda pode ser respondido
        if ($recurso['status'] !== 'aberto') {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Este recurso não pode mais ser respondido']);
            exit;
        }

        // Verificar se está no prazo para responder
        if (!Recurso::verificarPrazoRecurso($recurso['etapa_id'])) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'O prazo para responder a este recurso expirou']);
            exit;
        }

        // Atualizar a resposta do candidato e mudar status para "em_analise"
        $sucesso = Recurso::responderRecurso($recurso_id, $resposta);

        if ($sucesso) {
            echo json_encode(['success' => true, 'message' => 'Resposta enviada com sucesso. O status foi alterado para "Em análise".']);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    public function criar($inscricao_id, $etapa_id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        $inscricao = Inscricao::buscarPorId($inscricao_id);
        $etapa = PssEtapa::buscarPorId($etapa_id);

        if (!$inscricao || !$etapa || $inscricao['candidato_id'] != $candidato_id) {
            header('Location: /painel?erro=Inscrição ou etapa não encontrada');
            exit;
        }

        // Verificar se a etapa permite recurso e se está no prazo
        if (!Recurso::verificarPrazoRecurso($etapa_id)) {
            header('Location: /painel?erro=Prazo para recurso encerrado ou etapa não permite recurso');
            exit;
        }

        // Verificar se já existe recurso
        $recurso_existente = Recurso::verificarRecursoExistente($inscricao_id, $etapa_id);
        if ($recurso_existente) {
            header('Location: /painel?erro=Você já possui um recurso para esta etapa');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $protocolo = Recurso::gerarProtocolo();
            
            $dados = [
                'inscricao_id' => $inscricao_id,
                'etapa_id' => $etapa_id,
                'conteudo' => $_POST['conteudo'] ?? $_POST['justificativa'],
                'status' => 'aberto',
                'dt_abertura' => date('Y-m-d H:i:s'),
                'dt_decisao' => null,
                'motivo_decisao' => null,
                'avaliador_id' => null,
                'motivo_abertura' => null, // Será preenchido pelo avaliador quando indeferir
                'resposta_candidato' => $_POST['conteudo'] ?? $_POST['justificativa']
            ];

            if (Recurso::criar($dados)) {
                header("Location: /painel/recursos/{$inscricao_id}?success=Recurso enviado com sucesso! Protocolo: {$protocolo}");
                exit;
            } else {
                $erro = 'Erro ao enviar recurso';
            }
        }

        $this->view('candidato/criar_recurso', [
            'inscricao' => $inscricao,
            'etapa' => $etapa,
            'erro' => $erro ?? null
        ]);
    }

    public function consultar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $protocolo = $_POST['protocolo'] ?? '';
            
            if ($protocolo) {
                $recurso = Recurso::buscarPorProtocolo($protocolo);
                if ($recurso) {
                    $this->view('home/consulta_recurso', [
                        'recurso' => $recurso,
                        'protocolo' => $protocolo
                    ]);
                    return;
                } else {
                    $erro = 'Protocolo de recurso não encontrado';
                }
            } else {
                $erro = 'Digite um protocolo válido';
            }
        }

        $this->view('home/consulta_recurso', [
            'erro' => $erro ?? null,
            'protocolo' => $_POST['protocolo'] ?? ''
        ]);
    }

    // Métodos administrativos
    public function listarPendentes()
    {
        $recursos = Recurso::buscarPorStatus('em_analise');

        $this->view('admin/recursos/pendentes', [
            'recursos' => $recursos
        ]);
    }

    public function responder_admin($id)
    {
        $recurso = Recurso::buscarPorId($id);
        if (!$recurso) {
            header('Location: /admin/recursos?erro=Recurso não encontrado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resposta_avaliador = $_POST['resposta_avaliador'] ?? '';
            $decisao = $_POST['decisao'] ?? ''; // 'deferido' ou 'indeferido'
            $avaliador_id = $_SESSION['admin']['id'] ?? 1; // ID do avaliador logado

            if (!$resposta_avaliador || !$decisao) {
                $erro = 'Todos os campos são obrigatórios';
            } else {
                if (Recurso::finalizarRecurso($id, $resposta_avaliador, $decisao, $avaliador_id)) {
                    header('Location: /admin/recursos/pendentes?success=Recurso avaliado com sucesso');
                    exit;
                } else {
                    $erro = 'Erro ao avaliar recurso';
                }
            }
        }

        $this->view('admin/recursos/responder', [
            'recurso' => $recurso,
            'erro' => $erro ?? null
        ]);
    }

    public function listar($status = null)
    {
        if ($status) {
            $recursos = Recurso::buscarPorStatus($status);
        } else {
            $recursos = Recurso::buscarTodos();
        }

        $this->view('admin/recursos/listar', [
            'recursos' => $recursos,
            'status_filtro' => $status
        ]);
    }

    public function visualizar($id)
    {
        $recurso = Recurso::buscarPorId($id);
        if (!$recurso) {
            header('Location: /admin/recursos?erro=Recurso não encontrado');
            exit;
        }

        $this->view('admin/recursos/visualizar', [
            'recurso' => $recurso
        ]);
    }

    public function estatisticas()
    {
        $abertos = Recurso::contarPorStatus('aberto');
        $em_analise = Recurso::contarPorStatus('em_analise');
        $deferidos = Recurso::contarPorStatus('deferido');
        $indeferidos = Recurso::contarPorStatus('indeferido');

        $this->view('admin/recursos/estatisticas', [
            'abertos' => $abertos,
            'em_analise' => $em_analise,
            'deferidos' => $deferidos,
            'indeferidos' => $indeferidos
        ]);
    }

    public function exportar($status = null)
    {
        if ($status) {
            $recursos = Recurso::buscarPorStatus($status);
            $nome_arquivo = "recursos_{$status}_" . date('Y-m-d') . '.csv';
        } else {
            $recursos = Recurso::buscarTodos();
            $nome_arquivo = 'recursos_' . date('Y-m-d') . '.csv';
        }

        // Gerar CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');

        $output = fopen('php://output', 'w');
        
        // Cabeçalho do CSV
        fputcsv($output, [
            'ID',
            'Protocolo Inscrição',
            'Nome do Candidato',
            'CPF',
            'PSS',
            'Etapa',
            'Cargo',
            'Motivo Indeferimento',
            'Resposta Candidato',
            'Status',
            'Data Abertura',
            'Data Resposta Candidato',
            'Data Decisão',
            'Resposta Avaliador'
        ]);

        // Dados dos recursos
        foreach ($recursos as $recurso) {
            fputcsv($output, [
                $recurso['id'],
                $recurso['protocolo'],
                $recurso['candidato_nome'] ?? '',
                $recurso['candidato_cpf'] ?? '',
                $recurso['pss_titulo'] ?? '',
                $recurso['etapa_nome'],
                $recurso['cargo_nome'] ?? '',
                $recurso['motivo_abertura'],
                $recurso['resposta_candidato'],
                $recurso['status'],
                $recurso['dt_abertura'] ? date('d/m/Y H:i', strtotime($recurso['dt_abertura'])) : '',
                $recurso['dt_resposta_candidato'] ? date('d/m/Y H:i', strtotime($recurso['dt_resposta_candidato'])) : '',
                $recurso['dt_decisao'] ? date('d/m/Y H:i', strtotime($recurso['dt_decisao'])) : '',
                $recurso['motivo_decisao']
            ]);
        }

        fclose($output);
        exit;
    }

    public function etapasComRecurso()
    {
        $etapas = PssEtapa::buscarEtapasRecurso();

        $this->view('admin/recursos/etapas', [
            'etapas' => $etapas
        ]);
    }

    public function recursosPorEtapa($etapa_id)
    {
        $etapa = PssEtapa::buscarPorId($etapa_id);
        if (!$etapa) {
            header('Location: /admin/recursos?erro=Etapa não encontrada');
            exit;
        }

        $recursos = Recurso::buscarPorEtapa($etapa_id);

        $this->view('admin/recursos/por_etapa', [
            'etapa' => $etapa,
            'recursos' => $recursos
        ]);
    }
}

