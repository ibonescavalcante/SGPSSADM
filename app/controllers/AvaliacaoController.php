<?php

namespace App\controllers;

use App\models\Avaliacao;
use App\models\Inscricao;
use App\models\PssEtapa;
use App\models\Pss;
use App\models\PssCargo;

class AvaliacaoController extends Controller
{
    public function index()
    {
        // Listar etapas disponíveis para avaliação
        $etapas_abertas = PssEtapa::buscarEtapasAbertas();

        $this->view('admin/avaliacoes/index', [
            'etapas_abertas' => $etapas_abertas
        ]);
    }

    public function avaliarEtapa($etapa_id)
    {
        $etapa = PssEtapa::buscarPorId($etapa_id);
        if (!$etapa) {
            header('Location: /admin/avaliacoes?erro=Etapa não encontrada');
            exit;
        }

        $pss = Pss::buscarPorId($etapa['pss_id']);
        $avaliacoes = Avaliacao::buscarPorEtapa($etapa_id);

        $this->view('admin/avaliacoes/etapa', [
            'etapa' => $etapa,
            'pss' => $pss,
            'avaliacoes' => $avaliacoes
        ]);
    }

    public function criar($inscricao_id, $etapa_id)
    {
        $inscricao = Inscricao::buscarPorId($inscricao_id);
        $etapa = PssEtapa::buscarPorId($etapa_id);

        if (!$inscricao || !$etapa) {
            header('Location: /admin/avaliacoes?erro=Inscrição ou etapa não encontrada');
            exit;
        }

        // Verificar se já existe avaliação
        $avaliacao_existente = Avaliacao::verificarAvaliacaoExistente($inscricao_id, $etapa_id);
        if ($avaliacao_existente) {
            header("Location: /admin/avaliacoes/editar/{$avaliacao_existente['id']}");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'inscricao_id' => $inscricao_id,
                'pss_etapa_id' => $etapa_id,
                'nota' => $_POST['nota'],
                'observacoes' => $_POST['observacoes'] ?? '',
                'avaliador_nome' => $_POST['avaliador_nome'] ?? 'Administrador',
                'dt_avaliacao' => date('Y-m-d H:i:s'),
                'motivo_reprovacao' => $_POST['motivo_reprovacao'] ?? null
            ];

            if (Avaliacao::criar($dados)) {
                header("Location: /admin/avaliacoes/etapa/{$etapa_id}?success=Avaliação criada com sucesso");
                exit;
            } else {
                $erro = 'Erro ao criar avaliação';
            }
        }

        $this->view('admin/avaliacoes/criar', [
            'inscricao' => $inscricao,
            'etapa' => $etapa,
            'erro' => $erro ?? null
        ]);
    }

    public function editar($id)
    {
        $avaliacao = Avaliacao::buscarPorId($id);
        if (!$avaliacao) {
            header('Location: /admin/avaliacoes?erro=Avaliação não encontrada');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nota' => $_POST['nota'],
                'observacoes' => $_POST['observacoes'] ?? '',
                'avaliador_nome' => $_POST['avaliador_nome'] ?? 'Administrador',
                'dt_avaliacao' => date('Y-m-d H:i:s'),
                'motivo_reprovacao' => $_POST['motivo_reprovacao'] ?? null
            ];

            if (Avaliacao::atualizar($id, $dados)) {
                header("Location: /admin/avaliacoes/etapa/{$avaliacao['pss_etapa_id']}?success=Avaliação atualizada com sucesso");
                exit;
            } else {
                $erro = 'Erro ao atualizar avaliação';
            }
        }

        $this->view('admin/avaliacoes/editar', [
            'avaliacao' => $avaliacao,
            'erro' => $erro ?? null
        ]);
    }

    public function excluir($id)
    {
        $avaliacao = Avaliacao::buscarPorId($id);
        if (!$avaliacao) {
            header('Location: /admin/avaliacoes?erro=Avaliação não encontrada');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Avaliacao::excluir($id)) {
                header("Location: /admin/avaliacoes/etapa/{$avaliacao['pss_etapa_id']}?success=Avaliação excluída com sucesso");
                exit;
            } else {
                header("Location: /admin/avaliacoes/etapa/{$avaliacao['pss_etapa_id']}?erro=Erro ao excluir avaliação");
                exit;
            }
        }

        header('Location: /admin/avaliacoes');
        exit;
    }

    public function classificacao($pss_id, $cargo_id = null)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header('Location: /admin/avaliacoes?erro=PSS não encontrado');
            exit;
        }

        $classificacao = Avaliacao::buscarClassificacao($pss_id, $cargo_id);
        $cargos = PssCargo::buscarPorPss($pss_id);

        $this->view('admin/avaliacoes/classificacao', [
            'pss' => $pss,
            'classificacao' => $classificacao,
            'cargos' => $cargos,
            'cargo_selecionado' => $cargo_id
        ]);
    }

    public function exportarClassificacao($pss_id, $cargo_id = null)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header('Location: /admin/avaliacoes?erro=PSS não encontrado');
            exit;
        }

        $classificacao = Avaliacao::buscarClassificacao($pss_id, $cargo_id);

        $nome_arquivo = 'classificacao_' . $pss['titulo'] . '_' . date('Y-m-d') . '.csv';
        if ($cargo_id) {
            $cargo = PssCargo::buscarPorId($cargo_id);
            $nome_arquivo = 'classificacao_' . $cargo['nome'] . '_' . date('Y-m-d') . '.csv';
        }

        // Gerar CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');

        $output = fopen('php://output', 'w');
        
        // Cabeçalho do CSV
        fputcsv($output, [
            'Posição',
            'Protocolo',
            'Nome do Candidato',
            'CPF',
            'Cargo',
            'Nota Final',
            'Total de Avaliações'
        ]);

        // Dados da classificação
        $posicao = 1;
        foreach ($classificacao as $item) {
            fputcsv($output, [
                $posicao++,
                $item['protocolo'],
                $item['candidato_nome'],
                $item['candidato_cpf'],
                $item['cargo_nome'],
                number_format($item['nota_final'], 2, ',', '.'),
                $item['total_avaliacoes']
            ]);
        }

        fclose($output);
        exit;
    }

    public function avaliacaoLote($etapa_id)
    {
        $etapa = PssEtapa::buscarPorId($etapa_id);
        if (!$etapa) {
            header('Location: /admin/avaliacoes?erro=Etapa não encontrada');
            exit;
        }

        $pss = Pss::buscarPorId($etapa['pss_id']);
        $cargos = PssCargo::buscarPorPss($etapa['pss_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cargo_id = $_POST['cargo_id'] ?? null;
            $nota_padrao = $_POST['nota_padrao'] ?? 0;
            $observacoes = $_POST['observacoes'] ?? '';
            $avaliador_nome = $_POST['avaliador_nome'] ?? 'Administrador';

            // Buscar inscrições do cargo
            $inscricoes = Inscricao::buscarPorCargo($cargo_id);
            $sucesso = 0;
            $erros = 0;

            foreach ($inscricoes as $inscricao) {
                // Verificar se já existe avaliação
                $avaliacao_existente = Avaliacao::verificarAvaliacaoExistente($inscricao['id'], $etapa_id);
                if (!$avaliacao_existente) {
                    $dados = [
                        'inscricao_id' => $inscricao['id'],
                        'pss_etapa_id' => $etapa_id,
                        'nota' => $nota_padrao,
                        'observacoes' => $observacoes,
                        'avaliador_nome' => $avaliador_nome,
                        'dt_avaliacao' => date('Y-m-d H:i:s')
                    ];

                    if (Avaliacao::criar($dados)) {
                        $sucesso++;
                    } else {
                        $erros++;
                    }
                }
            }

            header("Location: /admin/avaliacoes/etapa/{$etapa_id}?success=Avaliação em lote concluída: {$sucesso} criadas, {$erros} erros");
            exit;
        }

        $this->view('admin/avaliacoes/lote', [
            'etapa' => $etapa,
            'pss' => $pss,
            'cargos' => $cargos
        ]);
    }
}

