<?php

namespace App\controllers;

use App\models\Pss;
use App\models\PssEtapa;
use App\models\PssCargo;
use App\models\Inscricao;

class PssController extends Controller
{
    public function index()
    {
        $pss_list = Pss::buscarTodos();
        $this->view("admin/pss/index", ["pss_list" => $pss_list]);
    }

    public function criar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "titulo" => $_POST["titulo"],
                "secretaria" => $_POST["secretaria"],
                "ano_exercicio" => $_POST["ano_exercicio"],
                "status_global" => $_POST["status_global"],
                "inscricao_ini" => $_POST["inscricao_ini"],
                "inscricao_fim" => $_POST["inscricao_fim"],
                "publicado_em" => $_POST["publicado_em"] ?? null,
                "versao" => $_POST["versao"] ?? "1.0",
                "metas_json" => $_POST["metas_json"] ?? null
            ];

            if (Pss::criar($dados)) {
                header("Location: /admin/pss?success=1");
                exit;
            } else {
                $erro = "Erro ao criar PSS";
            }
        }

        $this->view("admin/pss/criar", ["erro" => $erro ?? null]);
    }

    public function editar($id)
    {
        $pss = Pss::buscarPorId($id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "titulo" => $_POST["titulo"],
                "secretaria" => $_POST["secretaria"],
                "ano_exercicio" => $_POST["ano_exercicio"],
                "status_global" => $_POST["status_global"],
                "inscricao_ini" => $_POST["inscricao_ini"],
                "inscricao_fim" => $_POST["inscricao_fim"],
                "publicado_em" => $_POST["publicado_em"] ?? null,
                "versao" => $_POST["versao"],
                "metas_json" => $_POST["metas_json"] ?? null
            ];

            if (Pss::atualizar($id, $dados)) {
                header("Location: /admin/pss?success=2");
                exit;
            } else {
                $erro = "Erro ao atualizar PSS";
            }
        }

        $this->view("admin/pss/editar", ["pss" => $pss, "erro" => $erro ?? null]);
    }

    public function visualizar($id)
    {
        $pss = Pss::buscarPorId($id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        $etapas = PssEtapa::buscarPorPss($id);
        $cargos = PssCargo::buscarPorPss($id); // Buscar todos os cargos sem filtro de zona/microrregião

        $this->view("admin/pss/visualizar", [
            "pss" => $pss,
            "etapas" => $etapas,
            "cargos" => $cargos
        ]);
    }

    public function excluir($id)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (Pss::excluir($id)) {
                header("Location: /admin/pss?success=3");
                exit;
            } else {
                header("Location: /admin/pss?erro=Erro ao excluir PSS");
                exit;
            }
        }

        header("Location: /admin/pss");
        exit;
    }

    public function gerenciarEtapas($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        $etapas = PssEtapa::buscarPorPss($pss_id);

        $this->view("admin/pss/etapas", [
            "pss" => $pss,
            "etapas" => $etapas
        ]);
    }

    public function criarEtapa($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "pss_id" => $pss_id,
                "nome" => $_POST["nome"],
                "descricao" => $_POST["descricao"],
                "dt_ini" => $_POST["dt_ini"],
                "dt_fim" => $_POST["dt_fim"],
                "permite_recurso" => isset($_POST["permite_recurso"]) ? true : false,
                "rec_ini" => $_POST["rec_ini"] ?? null,
                "rec_fim" => $_POST["rec_fim"] ?? null,
                "peso" => $_POST["peso"] ?? 1
            ];

            if (PssEtapa::criar($dados)) {
                header("Location: /admin/pss/{$pss_id}/etapas?success=1");
                exit;
            } else {
                $erro = "Erro ao criar etapa";
            }
        }

        $this->view("admin/pss/criar_etapa", [
            "pss" => $pss,
            "erro" => $erro ?? null
        ]);
    }

    public function gerenciarCargos($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        $cargos = PssCargo::buscarPorPss($pss_id);

        $this->view("admin/pss/cargos", [
            "pss" => $pss,
            "cargos" => $cargos
        ]);
    }

    public function criarCargo($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /admin/pss?erro=PSS não encontrado");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "pss_id" => $pss_id,
                "nome" => $_POST["nome"],
                "secretaria" => $_POST["secretaria"],
                "vagas_total" => $_POST["vagas_total"],
                "vagas_pcd" => $_POST["vagas_pcd"] ?? 0,
                "vagas_ppp" => $_POST["vagas_ppp"] ?? 0,
                "cadastro_reserva" => isset($_POST["cadastro_reserva"]) ? true : false,
                "requisitos_texto" => $_POST["requisitos_texto"]
            ];

            if (PssCargo::criar($dados)) {
                header("Location: /admin/pss/{$pss_id}/cargos?success=1");
                exit;
            } else {
                $erro = "Erro ao criar cargo";
            }
        }

        $this->view("admin/pss/criar_cargo", [
            "pss" => $pss,
            "erro" => $erro ?? null
        ]);
    }

    public function publicar($id)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "status_global" => "em_andamento",
                "publicado_em" => date("Y-m-d H:i:s"),
                "titulo" => $_POST["titulo"] ?? "",
                "secretaria" => $_POST["secretaria"] ?? "",
                "ano_exercicio" => $_POST["ano_exercicio"] ?? date("Y"),
                "inscricao_ini" => $_POST["inscricao_ini"] ?? "",
                "inscricao_fim" => $_POST["inscricao_fim"] ?? "",
                "versao" => $_POST["versao"] ?? "1.0",
                "metas_json" => $_POST["metas_json"] ?? null
            ];

            if (Pss::atualizar($id, $dados)) {
                header("Location: /admin/pss?success=4");
                exit;
            } else {
                header("Location: /admin/pss?erro=Erro ao publicar PSS");
                exit;
            }
        }

        header("Location: /admin/pss");
        exit;
    }

    public function suspender($id)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "status_global" => "recurso_aberto",
                "titulo" => $_POST["titulo"] ?? "",
                "secretaria" => $_POST["secretaria"] ?? "",
                "ano_exercicio" => $_POST["ano_exercicio"] ?? date("Y"),
                "inscricao_ini" => $_POST["inscricao_ini"] ?? "",
                "inscricao_fim" => $_POST["inscricao_fim"] ?? "",
                "versao" => $_POST["versao"] ?? "1.0",
                "metas_json" => $_POST["metas_json"] ?? null,
                "publicado_em" => $_POST["publicado_em"] ?? null
            ];

            if (Pss::atualizar($id, $dados)) {
                header("Location: /admin/pss?success=5");
                exit;
            } else {
                header("Location: /admin/pss?erro=Erro ao suspender PSS");
                exit;
            }
        }

        header("Location: /admin/pss");
        exit;
    }
}

