<?php

namespace App\controllers;

use App\models\Pss;
use App\models\Secretaria;
use App\models\Zona;
use App\models\Microrregiao;
use App\models\CargaHoraria;
use App\models\NivelEscolaridade;
use App\models\TipoDocumento;

class AdminGerenciarPssController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION["admin"]) || $_SESSION["admin"]["perfil"] !== "admin") {
            header("Location: /admin/login");
            exit;
        }
    }

    public function index()
    {
        $pss_list = Pss::buscarTodos();

        $this->view("admin/pss/gerenciar-pss", [
            "pss_list" => $pss_list,
            "secretarias" => Secretaria::buscarTodos(),
            "zonas" => Zona::buscarTodos(),
            "microrregioes" => Microrregiao::buscarTodos(),
            "cargasHorarias" => CargaHoraria::buscarTodos(),
            "niveisEscolaridade" => NivelEscolaridade::buscarTodos(),
            "tiposDocumento" => TipoDocumento::buscarTodos()
        ]);
    }

    public function editar($id)
    {
        $pss = Pss::buscarPorId($id);
        if (!$pss) {
            header("Location: /admin/pss/gerenciar?erro=PSS não encontrado");
            exit;
        }

        // Decodificar metas_json para preencher o formulário
        $pss["metas"] = json_decode($pss["metas_json"], true);

        $this->view("admin/pss/gerenciar-pss", [
            "pss" => $pss,
            "secretarias" => Secretaria::buscarTodos(),
            "zonas" => Zona::buscarTodos(),
            "microrregioes" => Microrregiao::buscarTodos(),
            "cargasHorarias" => CargaHoraria::buscarTodos(),
            "niveisEscolaridade" => NivelEscolaridade::buscarTodos(),
            "tiposDocumento" => TipoDocumento::buscarTodos()
        ]);
    }

    public function salvar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $dados = [
                "id" => $_POST["id"] ?? null,
                "titulo" => $_POST["titulo"],
                "secretaria_id" => $_POST["secretaria_id"],
                "ano_exercicio" => $_POST["ano_exercicio"],
                "descricao" => $_POST["descricao"],
                "vagas_total" => $_POST["vagas_total"],
                "vagas_pcd" => $_POST["vagas_pcd"],
                "vagas_ppp" => $_POST["vagas_ppp"],
                "salario_base" => $_POST["salario_base"],
                "zona_id" => $_POST["zona"],
                "microrregiao_id" => $_POST["microrregiao"],
                "nivel_escolaridade_id" => $_POST["nivel_escolaridade_id"],
                "carga_horaria_id" => $_POST["carga_horaria_id"],
                "status_global" => $_POST["status_global"] ?? "em_andamento",
                "metas_json" => json_encode([
                    "config" => $_POST["config"] ?? [],
                    "etapas" => $_POST["etapas"] ?? [],
                    "cargos" => $_POST["cargos"] ?? [],
                    "documentacao" => $_POST["documentacao"] ?? [],
                    "criterios" => $_POST["criterios"] ?? []
                ])
            ];

            if (empty($dados["id"])) {
                // Criar novo PSS
                if (Pss::criar($dados)) {
                    header("Location: /admin/pss/gerenciar?success=PSS criado com sucesso");
                    exit;
                } else {
                    $erro = "Erro ao criar PSS";
                }
            } else {
                // Atualizar PSS existente
                if (Pss::atualizar($dados)) {
                    header("Location: /admin/pss/gerenciar?success=PSS atualizado com sucesso");
                    exit;
                } else {
                    $erro = "Erro ao atualizar PSS";
                }
            }
        }
        // Se houver erro ou não for POST, redirecionar ou mostrar formulário com erro
        $this->view("admin/pss/gerenciar-pss", [
            "erro" => $erro ?? null,
            "secretarias" => Secretaria::buscarTodos(),
            "zonas" => Zona::buscarTodos(),
            "microrregioes" => Microrregiao::buscarTodos(),
            "cargasHorarias" => CargaHoraria::buscarTodos(),
            "niveisEscolaridade" => NivelEscolaridade::buscarTodos(),
            "tiposDocumento" => TipoDocumento::buscarTodos()
        ]);
    }

    public function excluir($id)
    {
        if (Pss::excluir($id)) {
            header("Location: /admin/pss/gerenciar?success=PSS excluído com sucesso");
            exit;
        } else {
            header("Location: /admin/pss/gerenciar?erro=Erro ao excluir PSS");
            exit;
        }
    }
}


