<?php

namespace App\controllers;

use App\models\Inscricao;
use App\models\Candidato;
use App\models\Pss;
use App\models\PssCargo;
use App\models\Estado;
use App\models\Escolaridade;
use App\models\Genero;
use App\models\Nacionalidade;
use App\models\Naturalidade;

class InscricaoController extends Controller
{
    public function escolherZona($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /?erro=PSS não encontrado");
            exit;
        }

        // Redirecionar diretamente para a página de detalhes do PSS com todas as vagas
        header("Location: /pss/" . $pss_id);
        exit;
    }

    public function escolherVaga($pss_id, $zona)
    {
        $pss = Pss::buscarPorId($pss_id);
        if (!$pss) {
            header("Location: /?erro=PSS não encontrado");
            exit;
        }

        // Buscar cargos da zona específica
        $cargos = PssCargo::buscarPorPssEZona($pss_id, $zona);

        $this->view("inscricao/escolher_vaga", [
            "pss" => $pss,
            "zona" => $zona,
            "cargos" => $cargos
        ]);
    }

    // ✅ CORREÇÃO: Método inscrever que estava faltando
    public function inscrever($pss_id, $cargo_id)
    {
        error_log("DEBUG: inscrever chamado com pss_id=$pss_id, cargo_id=$cargo_id, METHOD=" . $_SERVER["REQUEST_METHOD"]);
        
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            // Salvar a intenção de inscrição na sessão
            $_SESSION["inscricao_pendente"] = [
                "pss_id" => $pss_id,
                "cargo_id" => $cargo_id
            ];
            header("Location: /login?redirect=inscricao");
            exit;
        }

        // Se for POST, processar a inscrição
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            error_log("DEBUG: Processando POST no inscrever");
            $this->processarInscricao($pss_id, $cargo_id);
            return;
        }

        // Se for GET, exibir formulário
        $this->uploadDocumentos($pss_id, $cargo_id);
    }

    public function iniciarInscricao($pss_id, $cargo_id)
    {
        error_log("DEBUG: iniciarInscricao chamado com pss_id=$pss_id, cargo_id=$cargo_id, METHOD=" . $_SERVER["REQUEST_METHOD"]);
        
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            // Salvar a intenção de inscrição na sessão
            $_SESSION["inscricao_pendente"] = [
                "pss_id" => $pss_id,
                "cargo_id" => $cargo_id
            ];
            header("Location: /login?redirect=inscricao");
            exit;
        }

        // Se for POST, processar a inscrição
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            error_log("DEBUG: Processando POST no iniciarInscricao");
            $this->processarInscricao($pss_id, $cargo_id);
            return;
        }

        // Se for GET, exibir formulário
        $this->uploadDocumentos($pss_id, $cargo_id);
    }

    public function processarInscricao($pss_id, $cargo_id)
    {
        error_log("DEBUG: processarInscricaoInterno chamado");
        
        $pss = Pss::buscarPorId($pss_id);
        $cargo = PssCargo::buscarPorId($cargo_id);

        if (!$pss || !$cargo) {
            header("Location: /?erro=PSS ou cargo não encontrado");
            exit;
        }

        // Verificar se as inscrições estão abertas
        require_once __DIR__ . '/../helpers/InscricaoValidacoes.php';
        $validacao_periodo = \App\controllers\InscricaoValidacoes::validarPeriodoInscricao($pss_id);
        
        if (!$validacao_periodo['valido']) {
            // Se for uma requisição AJAX, retornar JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($validacao_periodo);
                exit;
            }
            
            // Para requisições normais, redirecionar com dados para modal
            $_SESSION['modal_periodo_encerrado'] = $validacao_periodo;
            header("Location: /pss/" . $pss_id . "?modal=periodo_encerrado");
            exit;
        }

        $candidato_id = $_SESSION["usuario"]["id"];

        // Verificar se já existe inscrição para este PSS
        $inscricao_existente = Inscricao::verificarInscricaoExistente($pss_id, $candidato_id);
        if ($inscricao_existente) {
            error_log("DEBUG: Inscrição existente encontrada, redirecionando para comprovante");
            $inscricao_completa = Inscricao::buscarPorId($inscricao_existente['id']);
            if ($inscricao_completa && !empty($inscricao_completa['protocolo'])) {
                header("Location: /inscricao/comprovante/" . $inscricao_completa['protocolo']);
                exit;
            } else {
                header("Location: /painel?erro=Você já possui inscrição neste PSS");
                exit;
            }
        }

        // Verificar documentos obrigatórios
        $documentos_obrigatorios = ["documento_identidade", "comprovante_escolaridade"];
        $documentos_opcionais = ["certificados", "comprovante_experiencia_declaracoes"];

        // Buscar documentos específicos do cargo
        $documentos_cargo = \App\models\PssCargo::buscarDocumentosExigidos($cargo_id);
        foreach ($documentos_cargo as $doc_cargo) {
            if ($doc_cargo["obrigatorio"]) {
                $documentos_obrigatorios[] = $doc_cargo["tipo_documento_codigo"];
            } else {
                $documentos_opcionais[] = $doc_cargo["tipo_documento_codigo"];
            }
        }
        $laudo_pcd_path = null;
        $concorrer_pcd = $_POST["concorrer_pcd"] ?? "nao";

        // Verificar se deve enviar laudo PCD
        if ($concorrer_pcd === "sim") {
            if (!isset($_FILES["laudo_pcd"]) || $_FILES["laudo_pcd"]["error"] !== UPLOAD_ERR_OK) {
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Laudo PCD é obrigatório para concorrer às vagas PCD");
                exit;
            }
        }

        $documentos_enviados = [];

        foreach ($documentos_obrigatorios as $doc) {
            if (!isset($_FILES[$doc]) || $_FILES[$doc]["error"] !== UPLOAD_ERR_OK) {
                // Tratar uploads múltiplos
                if (is_array($_FILES[$doc]['name'])) {
                    $has_file = false;
                    foreach ($_FILES[$doc]['error'] as $error) {
                        if ($error === UPLOAD_ERR_OK) {
                            $has_file = true;
                            break;
                        }
                    }
                    if (!$has_file) {
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Documento " . $doc . " é obrigatório");
                        exit;
                    }
                } else {
                    header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Documento " . $doc . " é obrigatório");
                    exit;
                }
            }
        }

        // Criar diretório para documentos do candidato
        $base_upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/../uploads/";
        $upload_dir = $base_upload_dir . "documentos/" . $candidato_id . "/" . $pss_id . "_" . $cargo_id . "/";
        
        // Criar diretório base se não existir
        if (!is_dir($base_upload_dir)) {
            if (!mkdir($base_upload_dir, 0777, true)) {
                error_log("Erro ao criar diretório base: " . $base_upload_dir);
                $_SESSION['erro_inscricao'] = "Erro interno ao processar documentos. Tente novamente.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
            chmod($base_upload_dir, 0777);
        }
        
        // Criar diretório específico do candidato
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                error_log("Erro ao criar diretório: " . $upload_dir);
                error_log("Permissões do diretório pai: " . substr(sprintf('%o', fileperms(dirname($upload_dir))), -4));
                $_SESSION['erro_inscricao'] = "Erro interno ao processar documentos. Tente novamente.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
            chmod($upload_dir, 0777);
        }

        // Fazer upload dos documentos obrigatórios e opcionais
        $todos_documentos_para_upload = array_merge($documentos_obrigatorios, $documentos_opcionais);

        foreach ($todos_documentos_para_upload as $doc_ref) {
            if (isset($_FILES[$doc_ref])) {
                $files = $_FILES[$doc_ref];

                // Se for um upload de múltiplos arquivos (array de arquivos)
                if (is_array($files["name"])) {
                    $uploaded_paths = [];
                    foreach ($files["name"] as $key => $name) {
                        if ($files["error"][$key] === UPLOAD_ERR_OK) {
                            $file_tmp_name = $files["tmp_name"][$key];
                            $file_size = $files["size"][$key];

                            if ($file_size > 10 * 1024 * 1024) {
                                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Arquivo " . $name . " excede 10MB");
                                exit;
                            }

                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime_type = finfo_file($finfo, $file_tmp_name);
                            finfo_close($finfo);

                            if ($mime_type !== "application/pdf") {
                                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Arquivo " . $name . " deve ser PDF");
                                exit;
                            }

                            $extension = "pdf";
                            $filename = $doc_ref . "_" . time() . "_" . $key . "." . $extension;
                            $filepath = $upload_dir . $filename;

                            if (!is_readable($file_tmp_name)) {
                                error_log("Arquivo temporário não é legível: " . $file_tmp_name);
                                $_SESSION["erro_inscricao"] = "Erro ao processar documento: " . $name . ". Arquivo corrompido.";
                                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                                exit;
                            }

                            if (!is_writable($upload_dir)) {
                                error_log("Diretório não é gravável: " . $upload_dir);
                                $_SESSION["erro_inscricao"] = "Erro interno de permissões. Tente novamente.";
                                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                                exit;
                            }

                            if (move_uploaded_file($file_tmp_name, $filepath)) {
                                chmod($filepath, 0666);
                                $uploaded_paths[] = $filepath;
                                error_log("Arquivo movido com sucesso: " . $filepath);
                            } else {
                                error_log("Erro ao mover arquivo: " . $file_tmp_name . " para " . $filepath);
                                error_log("Erro do sistema: " . error_get_last()["message"]);
                                $_SESSION["erro_inscricao"] = "Erro ao processar documento: " . $name . ". Tente novamente.";
                                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                                exit;
                            }
                        }
                    }
                    if (!empty($uploaded_paths)) {
                        $documentos_enviados[$doc_ref] = $uploaded_paths;
                    }
                } else if ($files["error"] === UPLOAD_ERR_OK) { // Se for um upload de arquivo único
                    $file_tmp_name = $files["tmp_name"];
                    $file_size = $files["size"];
                    $file_name = $files["name"];

                    if ($file_size > 10 * 1024 * 1024) {
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Arquivo " . $file_name . " excede 10MB");
                        exit;
                    }

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file_tmp_name);
                    finfo_close($finfo);

                    if ($mime_type !== "application/pdf") {
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Arquivo " . $file_name . " deve ser PDF");
                        exit;
                    }

                    $extension = "pdf";
                    $filename = $doc_ref . "_" . time() . "." . $extension;
                    $filepath = $upload_dir . $filename;

                    if (!is_readable($file_tmp_name)) {
                        error_log("Arquivo temporário não é legível: " . $file_tmp_name);
                        $_SESSION["erro_inscricao"] = "Erro ao processar documento: " . $file_name . ". Arquivo corrompido.";
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                        exit;
                    }

                    if (!is_writable($upload_dir)) {
                        error_log("Diretório não é gravável: " . $upload_dir);
                        $_SESSION["erro_inscricao"] = "Erro interno de permissões. Tente novamente.";
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                        exit;
                    }

                    if (move_uploaded_file($file_tmp_name, $filepath)) {
                        chmod($filepath, 0666);
                        $documentos_enviados[$doc_ref] = $filepath;
                        error_log("Arquivo movido com sucesso: " . $filepath);
                    } else {
                        error_log("Erro ao mover arquivo: " . $file_tmp_name . " para " . $filepath);
                        error_log("Erro do sistema: " . error_get_last()["message"]);
                        $_SESSION["erro_inscricao"] = "Erro ao processar documento: " . $file_name . ". Tente novamente.";
                        header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                        exit;
                    }
                }
            }
        }

        // Upload do laudo PCD se necessário
        if ($concorrer_pcd === "sim" && isset($_FILES["laudo_pcd"]) && $_FILES["laudo_pcd"]["error"] === UPLOAD_ERR_OK) {
            $file = $_FILES["laudo_pcd"];
            
            // Validar tamanho do arquivo (10MB)
            if ($file["size"] > 10 * 1024 * 1024) {
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Laudo PCD excede 10MB");
                exit;
            }
            
            // Validar tipo do arquivo (apenas PDF)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);
            
            if ($mime_type !== "application/pdf") {
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao?erro=Laudo PCD deve ser PDF");
                exit;
            }
            
            $extension = "pdf";
            $filename = "laudo_pcd_" . time() . "." . $extension;
            $filepath = $upload_dir . $filename;
            
            // Verificar se o arquivo temporário existe e é legível
            if (!is_readable($file["tmp_name"])) {
                error_log("Laudo PCD temporário não é legível: " . $file["tmp_name"]);
                $_SESSION['erro_inscricao'] = "Erro ao processar laudo PCD. Arquivo corrompido.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
            
            // Verificar se o diretório é gravável
            if (!is_writable($upload_dir)) {
                error_log("Diretório não é gravável para laudo PCD: " . $upload_dir);
                $_SESSION['erro_inscricao'] = "Erro interno de permissões. Tente novamente.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
            
            if (move_uploaded_file($file["tmp_name"], $filepath)) {
                chmod($filepath, 0666);
                $laudo_pcd_path = $filepath;
                error_log("Laudo PCD movido com sucesso: " . $filepath);
            } else {
                error_log("Erro ao mover laudo PCD: " . $file["tmp_name"] . " para " . $filepath);
                error_log("Erro do sistema: " . error_get_last()["message"]);
                $_SESSION['erro_inscricao'] = "Erro ao processar laudo PCD. Tente novamente.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
        }

        // ✅ CORREÇÃO: Usar o método criarComDocumentos que existe no modelo
        $dados_inscricao = [
            "pss_id" => $pss_id,
            "cargo_id" => $cargo_id, // Será mapeado para pss_cargo_id no modelo
            "candidato_id" => $candidato_id,
            "concorrer_pcd" => $concorrer_pcd,
            "laudo_pcd" => $laudo_pcd_path,
            "documentos" => $documentos_enviados,
            "status" => "apta"
        ];

        error_log("DEBUG: Dados da inscrição: " . json_encode($dados_inscricao));

        try {
            $protocolo = Inscricao::criarComDocumentos($dados_inscricao);

            if ($protocolo) {
                error_log("DEBUG: Inscrição criada com sucesso, protocolo: " . $protocolo);
                header("Location: /inscricao/comprovante/" . $protocolo);
                exit;
            } else {
                error_log("DEBUG: Erro ao criar inscrição");
                $_SESSION["erro_inscricao"] = "Erro ao processar inscrição. Tente novamente.";
                header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
                exit;
            }
        } catch (\Exception $e) {
            error_log("DEBUG: Exceção ao criar inscrição: " . $e->getMessage());
            $_SESSION["erro_inscricao"] = $e->getMessage();
            header("Location: /pss/" . $pss_id . "/cargo/" . $cargo_id . "/inscricao");
            exit;
        }
    }

    public function uploadDocumentos($pss_id, $cargo_id)
    {
        error_log("DEBUG: uploadDocumentos chamado");
        
        // Verificar se o usuário está logado
        if (!isset($_SESSION["usuario"])) {
            header("Location: /login");
            exit;
        }

        $pss = Pss::buscarPorId($pss_id);
        $cargo = PssCargo::buscarPorId($cargo_id);

        if (!$pss || !$cargo) {
            header("Location: /?erro=PSS ou cargo não encontrado");
            exit;
        }

        // Verificar se as inscrições estão abertas
        require_once __DIR__ . '/../helpers/InscricaoValidacoes.php';
        $validacao_periodo = \App\controllers\InscricaoValidacoes::validarPeriodoInscricao($pss_id);
        
        if (!$validacao_periodo['valido']) {
            // Se for uma requisição AJAX, retornar JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($validacao_periodo);
                exit;
            }
            
            // Para requisições normais, redirecionar com dados para modal
            $_SESSION['modal_periodo_encerrado'] = $validacao_periodo;
            header("Location: /pss/" . $pss_id . "?modal=periodo_encerrado");
            exit;
        }

        $candidato_id = $_SESSION["usuario"]["id"];

        // Verificar se já existe inscrição para este PSS
        $inscricao_existente = Inscricao::verificarInscricaoExistente($pss_id, $candidato_id);
        if ($inscricao_existente) {
            error_log("DEBUG: Inscrição existente encontrada no uploadDocumentos");
            $inscricao_completa = Inscricao::buscarPorId($inscricao_existente['id']);
            if ($inscricao_completa && !empty($inscricao_completa['protocolo'])) {
                header("Location: /inscricao/comprovante/" . $inscricao_completa['protocolo']);
                exit;
            } else {
                header("Location: /painel?erro=Você já possui inscrição neste PSS");
                exit;
            }
        }

        // ✅ CORREÇÃO: Buscar dados completos do candidato
        $candidato = Candidato::buscarPorId($candidato_id);
        if (!$candidato) {
            header("Location: /painel?erro=Dados do candidato não encontrados");
            exit;
        }

        error_log("DEBUG: Exibindo view upload_documentos");
        $this->view("inscricao/upload_documentos", [
            "pss" => $pss,
            "cargo" => $cargo,
            "candidato" => $candidato  // ✅ CORREÇÃO: Variável candidato adicionada
        ]);
    }

    public function comprovante($protocolo, $formato = null)
    {
        error_log("DEBUG: comprovante chamado com protocolo: $protocolo");
        
        // Buscar dados da inscrição
        $inscricao = Inscricao::buscarPorProtocolo($protocolo);
        if (!$inscricao) {
            error_log("DEBUG: Inscrição não encontrada para protocolo: $protocolo");
            header("Location: /?erro=Comprovante não encontrado");
            exit;
        }

        error_log("DEBUG: Inscrição encontrada, exibindo comprovante");

        // Buscar dados completos
        $pss = Pss::buscarPorId($inscricao["pss_id"]);
        $cargo = PssCargo::buscarPorId($inscricao["pss_cargo_id"]);
        $candidato = Candidato::buscarPorId($inscricao["candidato_id"]);

        // Se for solicitado PDF
        if ($formato === "pdf") {
            $this->gerarComprovantePDF($inscricao, $pss, $cargo, $candidato);
            return;
        }

        $this->view("inscricao/comprovante", [
            "inscricao" => $inscricao,
            "pss" => $pss,
            "cargo" => $cargo,
            "candidato" => $candidato
        ]);
    }

    public function consultarProtocolo()
    {
        $erro = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $protocolo = trim($_POST["protocolo"] ?? "");
            
            if (!empty($protocolo)) {
                $inscricao = Inscricao::buscarPorProtocolo($protocolo);
                
                if ($inscricao) {
                    $this->view("home/consulta_protocolo", [
                        "inscricao" => $inscricao,
                        "protocolo" => $protocolo
                    ]);
                    return;
                } else {
                    $erro = "Protocolo não encontrado";
                }
            } else {
                $erro = "Digite um protocolo válido";
            }
        }

        $this->view("home/consulta_protocolo", [
            "erro" => $erro ?? null,
            "protocolo" => $_POST["protocolo"] ?? ""
        ]);
    }

    public function gerarComprovante($protocolo)
    {
        error_log("DEBUG: gerarComprovante chamado com protocolo=$protocolo");

        $inscricao = Inscricao::buscarPorProtocolo($protocolo);
        if (!$inscricao) {
            header("Location: /painel?erro=Comprovante não encontrado");
            exit;
        }

        $pss = Pss::buscarPorId($inscricao["pss_id"]);
        $cargo = PssCargo::buscarPorId($inscricao["pss_cargo_id"]);
        $candidato = Candidato::buscarPorId($inscricao["candidato_id"]);

        $this->view("inscricao/comprovante", [
            "inscricao" => $inscricao,
            "pss" => $pss,
            "cargo" => $cargo,
            "candidato" => $candidato
        ]);
    }

    private function gerarComprovantePDF($inscricao, $pss, $cargo, $candidato)
    {
        // Implementação simplificada
        header("Content-Type: text/html; charset=utf-8");
        echo "<h1>Comprovante de Inscrição</h1>";
        echo "<p>Protocolo: " . htmlspecialchars($inscricao["protocolo"]) . "</p>";
        echo "<p>Candidato: " . htmlspecialchars($candidato["nome"]) . "</p>";
        echo "<p>PSS: " . htmlspecialchars($pss["titulo"]) . "</p>";
        echo "<p>Cargo: " . htmlspecialchars($cargo["nome"]) . "</p>";
        exit;
    }
}
