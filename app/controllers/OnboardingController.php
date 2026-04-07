<?php

namespace App\controllers;

use App\models\Pss;
use App\models\PssCargo;

class OnboardingController extends Controller
{
    public function escolherZona($pss_id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            // Salvar a intenção de inscrição na sessão
            $_SESSION["inscricao_pendente"] = [
                "pss_id" => $pss_id,
                "etapa" => "escolher_zona"
            ];
            header("Location: /login?redirect=onboarding");
            exit;
        }

        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /?erro=PSS não encontrado");
            exit;
        }

        // ✅ NOVA VERIFICAÇÃO: Verificar se candidato já tem inscrição ativa
        $candidato_id = $_SESSION["usuario"]["id"];
        $inscricao_existente = \App\models\Inscricao::verificarInscricaoExistente($pss_id, $candidato_id);
        
        if ($inscricao_existente) {
            // Verificar se o PSS permite múltiplas inscrições
            if (!$pss["permite_multiplas_inscricoes"]) {
                header("Location: /pss/" . $pss_id . "?erro=" . urlencode("Você já possui uma inscrição ativa neste PSS. Múltiplas inscrições não são permitidas."));
                exit;
            } else {
                header("Location: /pss/" . $pss_id . "?erro=" . urlencode("Você já possui uma inscrição ativa neste PSS."));
                exit;
            }
        }

        // Buscar zonas disponíveis para este PSS
        $zonas = PssCargo::buscarZonasPorPss($pss_id);

        $this->view("onboarding/escolher_zona", [
            "pss" => $pss,
            "zonas" => $zonas
        ]);
    }

    public function escolherMicrorregiao($pss_id, $zona)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            header("Location: /login?redirect=onboarding");
            exit;
        }

        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /?erro=PSS não encontrado");
            exit;
        }

        // Buscar microrregiões disponíveis para esta zona
        $microrregioes = PssCargo::buscarMicrorregioesPorPss($pss_id, $zona);

        // Se não há microrregiões específicas, pular para a lista de vagas
        if (empty($microrregioes)) {
            header("Location: /pss/" . $pss_id . "/vagas?zona=" . $zona);
            exit;
        }

        $this->view("onboarding/escolher_microrregiao", [
            "pss" => $pss,
            "zona" => $zona,
            "microrregioes" => $microrregioes
        ]);
    }

    public function mostrarVagas($pss_id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            header("Location: /login?redirect=onboarding");
            exit;
        }

        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /?erro=PSS não encontrado");
            exit;
        }

        // Obter filtros da URL (zona e microrregião escolhidas no onboarding)
        $zona_filtro = $_GET["zona"] ?? "todas";
        $microrregiao_filtro = $_GET["microrregiao"] ?? "todas";

        // Buscar cargos com filtros aplicados
        $cargos = PssCargo::buscarPorPssEZona($pss_id, $zona_filtro, $microrregiao_filtro);

        // Contar inscrições por cargo
        foreach ($cargos as &$cargo) {
            $cargo["total_inscricoes"] = PssCargo::contarInscricoes($cargo["id"]);
        }

        $this->view("onboarding/vagas_filtradas", [
            "pss" => $pss,
            "cargos" => $cargos,
            "zona_filtro" => $zona_filtro,
            "microrregiao_filtro" => $microrregiao_filtro
        ]);
    }
}

