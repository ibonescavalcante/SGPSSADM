<?php

namespace App\Controllers;

use App\core\Database;
use App\Models\Pss;
use Exception;

class AdminPssNovoController extends Controller
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
        
        // Verificar se o usuário está logado como admin
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }
    }

    /**
     * Página principal para criar novo PSS
     */
    public function index()
    {
        $this->view('admin/pss/criar-novo', [
            'titulo' => 'Criar Novo PSS'
        ]);
    }

    /**
     * Lista todos os PSS com filtros e paginação
     */
    public function listar()
    {
        try {
            // Parâmetros de filtro
            $titulo = $_GET['titulo'] ?? '';
            $secretaria = $_GET['secretaria'] ?? '';
            $status = $_GET['status'] ?? '';
            $pagina = max(1, (int)($_GET['pagina'] ?? 1));
            $itensPorPagina = 10;
            $offset = ($pagina - 1) * $itensPorPagina;

            // Construir query base
            $whereConditions = [];
            $params = [];

            if (!empty($titulo)) {
                $whereConditions[] = "p.titulo ILIKE ?";
                $params[] = "%{$titulo}%";
            }

            if (!empty($secretaria)) {
                $whereConditions[] = "p.secretaria = ?";
                $params[] = $secretaria;
            }

            if (!empty($status)) {
                $whereConditions[] = "p.status_global = ?";
                $params[] = $status;
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Query para contar total de registros
            $sqlCount = "SELECT COUNT(*) FROM pss.pss p {$whereClause}";
            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->execute($params);
            $totalItens = $stmtCount->fetchColumn();

            // Query principal com dados dos PSS
            $sql = "SELECT 
                        p.id,
                        p.titulo,
                        p.secretaria,
                        p.ano_exercicio,
                        p.status_global,
                        p.criado_em,
                        p.atualizado_em,
                        COUNT(c.id) as total_cargos
                    FROM pss.pss p
                    LEFT JOIN pss.pss_cargo c ON p.id = c.pss_id
                    {$whereClause}
                    GROUP BY p.id, p.titulo, p.secretaria, p.ano_exercicio, p.status_global, p.criado_em, p.atualizado_em
                    ORDER BY p.criado_em DESC
                    LIMIT ? OFFSET ?";

            $params[] = $itensPorPagina;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $pssLista = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calcular dados de paginação
            $totalPaginas = ceil($totalItens / $itensPorPagina);

            return $this->view("admin/pss/listar", [
                "pss_lista" => $pssLista,
                "total_itens" => $totalItens,
                "total_paginas" => $totalPaginas,
                "pagina_atual" => $pagina,
                "itens_por_pagina" => $itensPorPagina
            ]);

        } catch (Exception $e) {
            error_log("Erro ao listar PSS: " . $e->getMessage());
            return $this->view("admin/pss/listar", [
                "pss_lista" => [],
                "total_itens" => 0,
                "total_paginas" => 1,
                "pagina_atual" => 1,
                "itens_por_pagina" => $itensPorPagina,
                "erro" => "Erro ao carregar lista de PSS"
            ]);
        }
    }

    /**
     * Exibe o formulário de criação de PSS
     */
    public function criar()
    {
        // Buscar dados auxiliares do banco
        $secretarias = $this->buscarSecretarias();
        $tiposDocumento = $this->buscarTiposDocumento();
        $microrregioes = $this->buscarMicrorregioes();
        $zonas = $this->buscarZonas();
        $cargasHorarias = $this->buscarCargasHorarias();
        $niveisEscolaridade = $this->buscarNiveisEscolaridade();
        
        // Carregar a view com o formulário
        return $this->view("admin/pss/criar_novo", [
            "secretarias" => $secretarias,
            "tipos_documento" => $tiposDocumento,
            "microrregioes" => $microrregioes,
            "zonas" => $zonas,
            "cargas_horarias" => $cargasHorarias,
            "niveis_escolaridade" => $niveisEscolaridade
        ]);
    }

    /**
     * Busca secretarias ativas do banco
     */
    private function buscarSecretarias()
    {
        try {
            $sql = "SELECT codigo, nome, sigla FROM pss.secretarias WHERE ativo = true ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar secretarias: " . $e->getMessage());
            // Retornar dados padrão em caso de erro
            return [
                ["codigo" => "SEMAD", "nome" => "Secretaria Municipal de Administração"],
                ["codigo" => "SEMED", "nome" => "Secretaria Municipal de Educação"],
                ["codigo" => "SEMSA", "nome" => "Secretaria Municipal de Saúde"]
            ];
        }
    }

    /**
     * Busca tipos de documento ativos do banco
     */
    private function buscarTiposDocumento()
    {
        try {
            $sql = "SELECT codigo, nome FROM pss.tipos_documento WHERE ativo = true ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar tipos de documento: " . $e->getMessage());
            // Retornar dados padrão em caso de erro
            return [
                ["codigo" => "RG", "nome" => "RG - Registro Geral"],
                ["codigo" => "CPF", "nome" => "CPF - Cadastro de Pessoa Física"],
                ["codigo" => "TITULO_ELEITOR", "nome" => "Título de Eleitor"]
            ];
        }
    }

    /**
     * Busca microrregiões ativas do banco
     */
    private function buscarMicrorregioes()
    {
        try {
            $sql = "SELECT codigo, nome FROM pss.microrregioes WHERE ativo = true ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar microrregiões: " . $e->getMessage());
            // Retornar dados padrão em caso de erro
            return [
                ["codigo" => "CENTRO", "nome" => "Centro"],
                ["codigo" => "NORTE", "nome" => "Norte"],
                ["codigo" => "SUL", "nome" => "Sul"]
            ];
        }
    }

    /**
     * Processa o salvamento do PSS
     */
    public function salvar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirectWithError("/admin/pss/criar-novo", "Método não permitido");
        }

        try {
            $this->db->beginTransaction();

            // Validar e processar dados básicos
            $dadosBasicos = $this->validarDadosBasicos($_POST);
            
            // Inserir PSS principal
            $pssId = $this->inserirPssPrincipal($dadosBasicos);
            
            // Processar etapas
            if (isset($_POST["etapas"]) && is_array($_POST["etapas"])) {
                $this->processarEtapas($pssId, $_POST["etapas"]);
            }
            
            // Processar cargos
            if (isset($_POST["cargos"]) && is_array($_POST["cargos"])) {
                $this->processarCargos($pssId, $_POST["cargos"]);
            }
            
            // Processar critérios de desempate
            if (isset($_POST["criterios"]) && is_array($_POST["criterios"])) {
                $this->processarCriterios($pssId, $_POST["criterios"]);
            }

            $this->db->commit();
            
            $this->redirectWithSuccess("/admin/pss/editar/{$pssId}", "PSS criado com sucesso!");

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao salvar PSS: " . $e->getMessage());
            $this->redirectWithError("/admin/pss/criar-novo", "Erro ao salvar PSS: " . $e->getMessage());
        }
    }

    /**
     * Valida os dados básicos do PSS
     */
    private function validarDadosBasicos($dados)
    {
        $erros = [];

        // Título obrigatório
        if (empty(trim($dados["titulo"] ?? ""))) {
            $erros[] = "Título do PSS é obrigatório";
        }

        // Ano de exercício obrigatório
                if (empty($dados["ano_exercicio"] ?? "")) {
            $erros[] = "Ano de exercício é obrigatório";
        } elseif (!is_numeric($dados["ano_exercicio"]) || $dados["ano_exercicio"] < 2024 || $dados["ano_exercicio"] > 2030) {
            $erros[] = "Ano de exercício deve estar entre 2024 e 2030";
        }

        if (!empty($erros)) {
            throw new Exception(implode(", ", $erros));
        }

        return [
            "titulo" => trim($dados["titulo"]),
            "secretaria" => trim($dados["secretaria"] ?? ""),
            "ano_exercicio" => (int)$dados["ano_exercicio"],
            "descricao" => trim($dados["descricao"] ?? ""),
            "status_global" => $dados["status_global"] ?? "rascunho",
            "permite_multiplas_inscricoes" => isset($dados["permite_multiplas_inscricoes"]),
            "configuracoes" => $this->processarConfiguracoes($dados)
        ];
    }

    /**
     * Processa as configurações adicionais
     */
    private function processarConfiguracoes($dados)
    {
        $config = [];
        
        if (isset($dados["config"]) && is_array($dados["config"])) {
            $config["exige_residencia"] = isset($dados["config"]["exige_residencia"]);
            $config["permite_recursos"] = isset($dados["config"]["permite_recursos"]);
            $config["permite_impugnacao"] = isset($dados["config"]["permite_impugnacao"]);
        }

        return json_encode($config);
    }

    /**
     * Insere o PSS principal no banco
     */
    private function inserirPssPrincipal($dados)
    {
        $sql = "INSERT INTO pss.pss (
                    titulo, secretaria, ano_exercicio, descricao, status_global,
                    permite_multiplas_inscricoes, configuracoes_json, 
                    criado_por, criado_em, atualizado_em
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dados["titulo"],
            $dados["secretaria"],
            $dados["ano_exercicio"],
            $dados["descricao"],
            $dados["status_global"],
            $dados["permite_multiplas_inscricoes"],
            $dados["configuracoes"],
            $_SESSION["admin_id"] ?? 1
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Processa as etapas do PSS
     */
    private function processarEtapas($pssId, $etapas)
    {
        foreach ($etapas as $ordem => $etapa) {
            if (empty(trim($etapa["nome"] ?? ""))) {
                continue; // Pular etapas sem nome
            }

            // Validar datas
            if (empty($etapa["dt_ini"]) || empty($etapa["dt_fim"])) {
                throw new Exception("Datas de início e fim são obrigatórias para a etapa: " . $etapa["nome"]);
            }

            $dtIni = new \DateTime($etapa["dt_ini"]);
            $dtFim = new \DateTime($etapa["dt_fim"]);

            if ($dtFim <= $dtIni) {
                throw new Exception("Data fim deve ser maior que data início na etapa: " . $etapa["nome"]);
            }

            $sql = "INSERT INTO pss.pss_etapa (
                        pss_id, nome, descricao, dt_ini, dt_fim, 
                        permite_recurso, rec_ini, rec_fim, peso, ordem
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $permiteRecurso = isset($etapa["permite_recurso"]);
            $recIni = !empty($etapa["rec_ini"]) ? $etapa["rec_ini"] : null;
            $recFim = !empty($etapa["rec_fim"]) ? $etapa["rec_fim"] : null;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $pssId,
                trim($etapa["nome"]),
                trim($etapa["descricao"] ?? ""),
                $etapa["dt_ini"],
                $etapa["dt_fim"],
                $permiteRecurso,
                $recIni,
                $recFim,
                $ordem + 1, // Peso baseado na ordem
                $ordem + 1  // Ordem
            ]);
        }
    }

    /**
     * Processa os cargos do PSS
     */
    private function processarCargos($pssId, $cargos)
    {
        foreach ($cargos as $cargo) {
            if (empty(trim($cargo["nome"] ?? ""))) {
                continue; // Pular cargos sem nome
            }

            // Validar vagas
            if (empty($cargo["vagas_total"]) || !is_numeric($cargo["vagas_total"]) || $cargo["vagas_total"] < 1) {
                throw new Exception("Número de vagas totais deve ser maior que zero para o cargo: " . $cargo["nome"]);
            }

            $sql = "INSERT INTO pss.pss_cargo (
                        pss_id, nome, secretaria, vagas_total, vagas_pcd, vagas_ppp,
                        cadastro_reserva, requisitos_texto, salario_base, zona, microrregiao,
                        nivel, carga_horaria, atribuicoes, criado_em, atualizado_em
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    RETURNING id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $pssId,
                trim($cargo["nome"]),
                trim($cargo["secretaria"] ?? ""),
                (int)$cargo["vagas_total"],
                (int)($cargo["vagas_pcd"] ?? 0),
                (int)($cargo["vagas_ppp"] ?? 0),
                isset($cargo["cadastro_reserva"]),
                trim($cargo["requisitos_texto"] ?? ""),
                !empty($cargo["salario_base"]) ? (float)$cargo["salario_base"] : null,
                $cargo["zona"] ?? "urbana",
                trim($cargo["microrregiao"] ?? ""),
                trim($cargo["nivel"] ?? ""),
                trim($cargo["carga_horaria"] ?? ""),
                trim($cargo["atribuicoes"] ?? "")
            ]);

            $cargoId = $stmt->fetchColumn();

            // Processar documentos do cargo
            if (!empty($cargo["documentacao_exigida"])) {
                $this->processarDocumentosCargo($cargoId, $cargo["documentacao_exigida"]);
            }
        }
    }

    /**
     * Processa os documentos de um cargo
     */
    private function processarDocumentosCargo($cargoId, $documentacaoJson)
    {
        $documentos = json_decode($documentacaoJson, true);
        if (!is_array($documentos)) {
            return;
        }

        foreach ($documentos as $doc) {
            if (empty($doc["nome"])) {
                continue;
            }

            $sql = "INSERT INTO pss.pss_cargo_doc (
                        pss_cargo_id, nome_doc, obrigatorio, 
                        criado_em, atualizado_em
                    ) VALUES (?, ?, ?, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $cargoId,
                $doc["nome"],
                $doc["obrigatorio"] ?? true
            ]);
        }
    }

    /**
     * Processa os critérios de desempate
     */
    private function processarCriterios($pssId, $criterios)
    {
        foreach ($criterios as $ordem => $criterio) {
            if (empty(trim($criterio["criterio"] ?? ""))) {
                continue; // Pular critérios vazios
            }

            $sql = "INSERT INTO pss.pss_desempate (pss_id, ordem, criterio) VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $pssId,
                $ordem + 1,
                trim($criterio["criterio"])
            ]);
        }
    }

    /**
     * Edita um PSS existente
     */
    public function editar($id)
    {
        try {
            // Buscar dados do PSS
            $pss = $this->buscarPss($id);
            if (!$pss) {
                $this->redirectWithError("/admin/dashboard", "PSS não encontrado");
            }

            // Buscar dados relacionados
            $etapas = $this->buscarEtapas($id);
            $cargos = $this->buscarCargos($id);
            $criterios = $this->buscarCriterios($id);

            // Buscar dados auxiliares
            $secretarias = $this->buscarSecretarias();
            $tiposDocumento = $this->buscarTiposDocumento();
            $microrregioes = $this->buscarMicrorregioes();
            $zonas = $this->buscarZonas();
            $cargasHorarias = $this->buscarCargasHorarias();
            $niveisEscolaridade = $this->buscarNiveisEscolaridade();

            return $this->view("admin/pss/editar", [
                "pss" => $pss,
                "etapas" => $etapas,
                "cargos" => $cargos,
                "criterios" => $criterios,
                "secretarias" => $secretarias,
                "tipos_documento" => $tiposDocumento,
                "microrregioes" => $microrregioes,
                "zonas" => $zonas,
                "cargas_horarias" => $cargasHorarias,
                "niveis_escolaridade" => $niveisEscolaridade
            ]);

        } catch (Exception $e) {
            error_log("Erro ao carregar PSS para edição: " . $e->getMessage());
            $this->redirectWithError("/admin/dashboard", "Erro ao carregar PSS");
        }
    }

    /**
     * Atualiza um PSS existente
     */
    public function atualizar($id)
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirectWithError("/admin/pss/editar/{$id}", "Método não permitido");
        }

        try {
            $this->db->beginTransaction();

            // Validar dados básicos
            $dadosBasicos = $this->validarDadosBasicos($_POST);
            
            // Atualizar PSS principal
            $this->atualizarPssPrincipal($id, $dadosBasicos);
            
            // Remover dados antigos
            $this->removerDadosAntigos($id);
            
            // Processar novos dados
            if (isset($_POST["etapas"]) && is_array($_POST["etapas"])) {
                $this->processarEtapas($id, $_POST["etapas"]);
            }
            
            if (isset($_POST["cargos"]) && is_array($_POST["cargos"])) {
                $this->processarCargos($id, $_POST["cargos"]);
            }
            
            if (isset($_POST["criterios"]) && is_array($_POST["criterios"])) {
                $this->processarCriterios($id, $_POST["criterios"]);
            }

            $this->db->commit();
            
            $this->redirectWithSuccess("/admin/pss/editar/{$id}", "PSS atualizado com sucesso!");

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao atualizar PSS: " . $e->getMessage());
            $this->redirectWithError("/admin/pss/editar/{$id}", "Erro ao atualizar PSS: " . $e->getMessage());
        }
    }

    /**
     * Busca dados do PSS
     */
    private function buscarPss($id)
    {
        $sql = "SELECT * FROM pss.pss WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca etapas de um PSS
     */
    private function buscarEtapas($pssId)
    {
        $sql = "SELECT * FROM pss.pss_etapa WHERE pss_id = ? ORDER BY ordem";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca cargos de um PSS
     */
    private function buscarCargos($pssId)
    {
        $sql = "SELECT * FROM pss.pss_cargo WHERE pss_id = ? ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);
        $cargos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($cargos as &$cargo) {
            $cargo["documentacao_exigida"] = $this->buscarDocumentosCargo($cargo["id"]);
        }
        return $cargos;
    }

    /**
     * Busca documentos de um cargo
     */
    private function buscarDocumentosCargo($cargoId)
    {
        $sql = "SELECT nome_doc as nome, obrigatorio FROM pss.pss_cargo_doc WHERE pss_cargo_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cargoId]);
        return json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Busca critérios de desempate de um PSS
     */
    private function buscarCriterios($pssId)
    {
        $sql = "SELECT criterio FROM pss.pss_desempate WHERE pss_id = ? ORDER BY ordem";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza o PSS principal no banco
     */
    private function atualizarPssPrincipal($id, $dados)
    {
        $sql = "UPDATE pss.pss SET 
                    titulo = ?, secretaria = ?, ano_exercicio = ?, descricao = ?, status_global = ?,
                    permite_multiplas_inscricoes = ?, configuracoes_json = ?, 
                    atualizado_por = ?, atualizado_em = NOW()
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dados["titulo"],
            $dados["secretaria"],
            $dados["ano_exercicio"],
            $dados["descricao"],
            $dados["status_global"],
            $dados["permite_multiplas_inscricoes"],
            $dados["configuracoes"],
            $_SESSION["admin_id"] ?? 1,
            $id
        ]);
    }

    /**
     * Remove dados antigos antes de inserir novos
     */
    private function removerDadosAntigos($pssId)
    {
        // Remover documentos dos cargos primeiro (FK)
        $sql = "DELETE FROM pss.pss_cargo_doc WHERE pss_cargo_id IN (SELECT id FROM pss.pss_cargo WHERE pss_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);

        // Remover etapas (exceto a primeira que é obrigatória)
        $sql = "DELETE FROM pss.pss_etapa WHERE pss_id = ? AND ordem > 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);

        // Remover cargos
        $sql = "DELETE FROM pss.pss_cargo WHERE pss_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);

        // Remover critérios
        $sql = "DELETE FROM pss.pss_desempate WHERE pss_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$pssId]);
    }



    /**
     * Exclui um PSS
     */
    public function excluir($id)
    {
        try {
            $this->db->beginTransaction();

            // Verificar se o PSS existe
            $pss = $this->buscarPss($id);
            if (!$pss) {
                throw new Exception("PSS não encontrado");
            }

            // Verificar se há inscrições
            $sql = "SELECT COUNT(*) as total FROM pss.inscricao WHERE pss_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result["total"] > 0) {
                throw new Exception("Não é possível excluir PSS com inscrições");
            }

            // Remover dados relacionados
            $this->removerDadosAntigos($id);

            // Remover PSS principal
            $sql = "DELETE FROM pss.pss WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            
            $this->redirectWithSuccess("/admin/pss/listar", "PSS excluído com sucesso!");

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao excluir PSS: " . $e->getMessage());
            $this->redirectWithError("/admin/pss/listar", "Erro ao excluir PSS: " . $e->getMessage());
        }
    }

    /**
     * Duplica um PSS existente
     */
    public function duplicar($id)
    {
        try {
            $this->db->beginTransaction();

            // Buscar PSS original
            $pssOriginal = $this->buscarPss($id);
            if (!$pssOriginal) {
                throw new Exception("PSS não encontrado");
            }

            // Criar novo PSS baseado no original
            $dadosNovo = [
                "titulo" => $pssOriginal["titulo"] . " (Cópia)",
                "secretaria" => $pssOriginal["secretaria"],
                "ano_exercicio" => date("Y"),
                "descricao" => $pssOriginal["descricao"],
                "status_global" => "rascunho",
                "permite_multiplas_inscricoes" => $pssOriginal["permite_multiplas_inscricoes"],
                "configuracoes" => $pssOriginal["configuracoes_json"]
            ];

            $novoPssId = $this->inserirPssPrincipal($dadosNovo);

            // Duplicar etapas
            $etapas = $this->buscarEtapas($id);
            foreach ($etapas as $etapa) {
                unset($etapa["id"]);
                $etapa["pss_id"] = $novoPssId;
                // Resetar datas para futuro
                $etapa["dt_ini"] = null;
                $etapa["dt_fim"] = null;
                $etapa["rec_ini"] = null;
                $etapa["rec_fim"] = null;
            }
            if (!empty($etapas)) {
                $this->processarEtapas($novoPssId, $etapas);
            }

            // Duplicar cargos
            $cargos = $this->buscarCargos($id);
            foreach ($cargos as &$cargo) {
                unset($cargo["id"]);
                $cargo["pss_id"] = $novoPssId;
            }
            if (!empty($cargos)) {
                $this->processarCargos($novoPssId, $cargos);
            }

            // Duplicar critérios
            $criterios = $this->buscarCriterios($id);
            foreach ($criterios as &$criterio) {
                unset($criterio["id"]);
                $criterio["pss_id"] = $novoPssId;
            }
            if (!empty($criterios)) {
                $this->processarCriterios($novoPssId, $criterios);
            }

            $this->db->commit();
            
            $this->redirectWithSuccess("/admin/pss/editar/{$novoPssId}", "PSS duplicado com sucesso!");

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erro ao duplicar PSS: " . $e->getMessage());
            $this->redirectWithError("/admin/pss/listar", "Erro ao duplicar PSS: " . $e->getMessage());
        }
    }

    /**
     * Métodos auxiliares para redirecionamento
     */
    private function redirectWithSuccess($url, $message)
    {
        $_SESSION["sucesso"] = $message;
        header("Location: {$url}");
        exit;
    }

    private function redirectWithError($url, $message)
    {
        $_SESSION["erro"] = $message;
        header("Location: {$url}");
        exit;
    }

    /**
     * Busca zonas ativas do banco
     */
    private function buscarZonas()
    {
        try {
            $sql = "SELECT id, nome FROM pss.zona ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar zonas: " . $e->getMessage());
            return [
                ["id" => 1, "nome" => "Urbana"],
                ["id" => 2, "nome" => "Rural"]
            ];
        }
    }

    /**
     * Busca cargas horárias ativas do banco
     */
    private function buscarCargasHorarias()
    {
        try {
            $sql = "SELECT id, horas FROM pss.carga_horaria ORDER BY horas";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar cargas horárias: " . $e->getMessage());
            return [
                ["id" => 1, "horas" => "20h"],
                ["id" => 2, "horas" => "30h"],
                ["id" => 3, "horas" => "40h"],
                ["id" => 4, "horas" => "44h"]
            ];
        }
    }

    /**
     * Busca níveis de escolaridade ativos do banco
     */
    private function buscarNiveisEscolaridade()
    {
        try {
            $sql = "SELECT id, nome FROM pss.nivel_escolaridade ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar níveis de escolaridade: " . $e->getMessage());
            return [
                ["id" => 1, "nome" => "Fundamental"],
                ["id" => 2, "nome" => "Médio"],
                ["id" => 3, "nome" => "Técnico"],
                ["id" => 4, "nome" => "Superior"],
                ["id" => 5, "nome" => "Pós-graduação"]
            ];
        }
    }
    
    
    public function visualizar($id) {
        try {
            // Buscar dados do PSS
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       COUNT(c.id) as total_cargos
                FROM pss p
                LEFT JOIN cargos c ON p.id = c.pss_id
                WHERE p.id = ?
                GROUP BY p.id
            ");
            $stmt->execute([$id]);
            $pss = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pss) {
                header('Location: /admin/pss');
                exit;
            }
            
            $this->view("admin/pss/visualizar", [
                "pss" => $pss
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao visualizar PSS: " . $e->getMessage());
            header('Location: /admin/pss');
            exit;
        }
    }
    
    public function inativar($id) {
        try {
            // Verificar se o PSS existe
            $stmt = $this->db->prepare("SELECT id, titulo FROM pss WHERE id = ?");
            $stmt->execute([$id]);
            $pss = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pss) {
                header('Content-Type: application/json');
                echo json_encode(['sucesso' => false, 'erro' => 'PSS não encontrado']);
                return;
            }
            
            // Inativar o PSS (definir status como 'inativo')
            $stmt = $this->db->prepare("UPDATE pss SET status_global = 'inativo', atualizado_em = NOW() WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                header('Content-Type: application/json');
                echo json_encode(['sucesso' => true, 'mensagem' => 'PSS inativado com sucesso']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['sucesso' => false, 'erro' => 'Erro ao inativar PSS']);
            }
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

}

