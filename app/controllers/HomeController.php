<?php

namespace App\controllers;

use App\models\Pss;
use App\models\PssCargo;

class HomeController extends Controller
{
    public function index()
    {
        // Verificar se há filtro de status
        $status_filtro = $_GET['status'] ?? 'todos';
        
        // Buscar processos conforme filtro
        if ($status_filtro === 'todos') {
            $pss_ativos = Pss::buscarTodos();
        } else {
            $pss_ativos = Pss::buscarPorStatus($status_filtro);
        }

        $this->view('home/processos_disponiveis', [
            'pss_ativos' => $pss_ativos,
            'status_filtro' => $status_filtro
        ]);
    }

    public function pssDetalhes($id)
    {
        $pss = Pss::buscarPorId($id);
        if (!$pss) {
            header('Location: /?erro=PSS não encontrado');
            exit;
        }

        // Buscar cargos com contagem de inscrições em uma única consulta otimizada
        $cargos = PssCargo::buscarPorPssComInscricoes($id);

        $this->view('home/pss_detalhes', [
            'pss' => $pss,
            'cargos' => $cargos
        ]);
    }

    public function cargoDetalhes($pss_id, $cargo_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        $cargo = PssCargo::buscarPorId($cargo_id);

        if (!$pss || !$cargo) {
            header('Location: /?erro=Cargo não encontrado');
            exit;
        }

        // Buscar contagem de inscrições apenas se necessário
        $cargo['total_inscricoes'] = PssCargo::contarInscricoes($cargo_id);

        $this->view('home/cargo_detalhes', [
            'pss' => $pss,
            'cargo' => $cargo
        ]);
    }
}
