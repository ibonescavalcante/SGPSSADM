<?php

namespace App\controllers;

use App\models\Pss;
use App\models\Inscricao;
use App\helpers\InscricaoHelper;
// use App\models\Documento;
use App\models\Recurso;
use App\models\Avaliacao;
use App\models\Candidato;
use App\models\Usuario; // Adicionado para usar o modelo Usuario

class AdminController extends Controller
{
    public function __construct()
    {
        // Chamar o construtor da classe pai para inicializar o Plates
        parent::__construct();
        
        // A verificação de sessão será feita por método para permitir acesso ao login
    }

    public function index()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        try {
            $db = \App\core\Database::getInstance();
            
            // Estatísticas gerais com consultas SQL diretas
            $total_pss = 0;
            $total_inscricoes = 0;
            $recursos_pendentes = 0;
            
            // Contar PSS ativos
            try {
                $stmt = $db->query("SELECT COUNT(*) as total FROM pss.pss WHERE status_global = 'em_andamento'");
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $total_pss = (int)($result['total'] ?? 0);
            } catch (\Exception $e) {
                error_log("Erro ao contar PSS: " . $e->getMessage());
            }

            // Contar inscrições
            try {
                $stmt = $db->query("SELECT COUNT(*) as total FROM pss.inscricao");
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $total_inscricoes = (int)($result['total'] ?? 0);
            } catch (\Exception $e) {
                error_log("Erro ao contar inscrições: " . $e->getMessage());
            }

            // Contar recursos pendentes
            try {
                $stmt = $db->query("SELECT COUNT(*) as total FROM pss.recurso WHERE status = 'aberto'");
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $recursos_pendentes = (int)($result['total'] ?? 0);
            } catch (\Exception $e) {
                error_log("Erro ao contar recursos: " . $e->getMessage());
            }

            // PSS recentes
            $pss_recentes = [];
            try {
                $stmt = $db->query("
                    SELECT id, titulo, secretaria, ano_exercicio, status_global, criado_em 
                    FROM pss.pss 
                    ORDER BY criado_em DESC 
                    LIMIT 5
                ");
                $pss_recentes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                error_log("Erro ao buscar PSS recentes: " . $e->getMessage());
            }

            // Inscrições recentes
            $inscricoes_recentes = [];
            try {
                $stmt = $db->query("
                    SELECT i.id, i.protocolo, i.status, i.dt_inscricao,
                           c.nome as candidato_nome
                    FROM pss.inscricao i
                    LEFT JOIN pss.candidato c ON i.candidato_id = c.id
                    ORDER BY i.dt_inscricao DESC 
                    LIMIT 5
                ");
                $inscricoes_recentes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                error_log("Erro ao buscar inscrições recentes: " . $e->getMessage());
            }

            // Dados para gráfico (últimos 6 meses)
            $meses = [];
            $dados_inscricoes = [];

            try {
                for ($i = 5; $i >= 0; $i--) {
                    $data_inicio = date("Y-m-01", strtotime("-{$i} months"));
                    $data_fim = date("Y-m-t", strtotime("-{$i} months"));
                    $mes_nome = date("M", strtotime("-{$i} months"));
                    
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as total 
                        FROM pss.inscricao 
                        WHERE dt_inscricao BETWEEN :inicio AND :fim
                    ");
                    $stmt->execute([
                        'inicio' => $data_inicio,
                        'fim' => $data_fim
                    ]);
                    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    $meses[] = $mes_nome;
                    $dados_inscricoes[] = (int)($result['total'] ?? 0);
                }
            } catch (\Exception $e) {
                error_log("Erro ao buscar dados do gráfico: " . $e->getMessage());
                // Dados padrão em caso de erro
                $meses = ['Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                $dados_inscricoes = [0, 0, 0, 0, 0, 0];
            }

        } catch (\Exception $e) {
            error_log("Erro geral no dashboard: " . $e->getMessage());
            // Valores padrão em caso de erro
            $total_pss = 0;
            $total_inscricoes = 0;
            $recursos_pendentes = 0;
            $pss_recentes = [];
            $inscricoes_recentes = [];
            $meses = ['Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            $dados_inscricoes = [0, 0, 0, 0, 0, 0];
        }

        $this->view("admin/dashboard", [
            "total_pss"            => $total_pss,
            "total_inscricoes"     => $total_inscricoes,
            "recursos_pendentes"   => $recursos_pendentes,
            "pss_recentes"         => $pss_recentes,
            "inscricoes_recentes"  => $inscricoes_recentes,
            "meses"                => $meses,
            "dados_inscricoes"     => $dados_inscricoes
        ]);
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email"] ?? "";
            $senha = $_POST["senha"] ?? "";

            $usuario = Usuario::verificarSenha($email, $senha);

            if ($usuario) {
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_id"] = $usuario["id"];
                $_SESSION["admin_nome"] = $usuario["nome"];
                $_SESSION["admin_email"] = $usuario["email"];
                header("Location: /admin");
                exit;
            } else {
                $erro = "Email ou senha inválidos";
            }
        }

        $this->view("admin/admin_login", [
            "erro" => $erro ?? null
        ]);
    }

    public function logout()
    {
        unset($_SESSION["admin_logged_in"], $_SESSION["admin_id"], $_SESSION["admin_nome"], $_SESSION["admin_email"]);
        session_destroy();
        header("Location: /admin/login");
        exit;
    }

    public function relatorios()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        // Relatórios gerais
        $estatisticas = [
            "total_candidatos"  => Candidato::contarTodos(),
            "total_pss"         => Pss::contarTodos(),
            "total_inscricoes"  => InscricaoHelper::contarTodas(),
            // "total_documentos"   => Documento::contarTodos(),
            "total_avaliacoes"  => Avaliacao::contarTodas(),
            "total_recursos"    => Recurso::contarTodos()
        ];

        // Estatísticas por status
        $inscricoes_por_status = Inscricao::contarPorStatus();
        // $documentos_por_status = Documento::contarPorStatusTodos();
        $recursos_por_status   = Recurso::contarPorStatusTodos();

        // PSS por status
        $pss_por_status = Pss::contarPorStatus();

        $this->view("admin/relatorios", [
            "estatisticas"           => $estatisticas,
            "inscricoes_por_status"  => $inscricoes_por_status,
            "recursos_por_status"    => $recursos_por_status,
            "pss_por_status"         => $pss_por_status
        ]);
    }

    public function exportarDados()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        $tipo = $_GET["tipo"] ?? "geral";

        switch ($tipo) {
            case "candidatos":
                $this->streamCsv("candidatos_" . date("Y-m-d") . ".csv", $this->csvCandidatos());
                break;
            case "inscricoes":
                $this->streamCsv("inscricoes_" . date("Y-m-d") . ".csv", $this->csvInscricoes());
                break;
            // case "documentos":
            //     $this->streamCsv("documentos_" . date("Y-m-d") . ".csv", $this->csvDocumentos());
            //     break;
            case "recursos":
                $this->streamCsv("recursos_" . date("Y-m-d") . ".csv", $this->csvRecursos());
                break;
            default:
                $this->exportarGeral();
        }
    }

    /* Exemplo de como deixar a exportação de documentos preparada
    private function csvDocumentos(): string
    {
        $documentos = Documento::buscarTodosCompletos();
        $f = fopen('php://temp', 'r+');
        fputcsv($f, ["Protocolo Inscrição","Candidato","Tipo Documento","Nome Arquivo","Status","Data Envio","Observações"]);
        foreach ($documentos as $d) {
            fputcsv($f, [
                $d["protocolo"],
                $d["candidato_nome"],
                $d["tipo"],
                $d["nome_arquivo"],
                $d["status_validacao"],
                date("d/m/Y H:i", strtotime($d["criado_em"])),
                $d["observacoes"] ?? ""
            ]);
        }
        rewind($f);
        return stream_get_contents($f);
    }
    */

    /** ------------ CSV helpers (retornam string) ------------ */

    private function csvCandidatos(): string
    {
        $candidatos = Candidato::buscarTodos();
        $f = fopen('php://temp', 'r+');
        fputcsv($f, ["ID","Nome","CPF","Email","Data Nascimento","Gênero","Escolaridade","Celular","PCD","PPP","Data Cadastro"]);
        foreach ($candidatos as $c) {
            fputcsv($f, [
                $c["id"],
                $c["nome"],
                $c["cpf"],
                $c["email"],
                $c["data_nascimento"],
                $c["genero_nome"] ?? "",
                $c["escolaridade_nome"] ?? "",
                $c["celular"],
                !empty($c["pcd"]) ? "Sim" : "Não",
                !empty($c["ppp"]) ? "Sim" : "Não",
                $c["criado_em"]
            ]);
        }
        rewind($f);
        return stream_get_contents($f);
    }

    private function csvInscricoes(): string
    {
        $inscricoes = Inscricao::buscarTodasCompletas();
        $f = fopen('php://temp', 'r+');
        fputcsv($f, ["Protocolo","PSS","Cargo","Candidato","CPF","Status","Data Inscrição","Nota Final"]);
        foreach ($inscricoes as $i) {
            $nota_final = Avaliacao::calcularNotaFinal($i["id"]);
            fputcsv($f, [
                $i["protocolo"],
                $i["pss_titulo"],
                $i["cargo_nome"],
                $i["candidato_nome"],
                $i["candidato_cpf"],
                $i["status"],
                date("d/m/Y H:i", strtotime($i["dt_inscricao"])),
                $nota_final["nota_final"] ?? "N/A"
            ]);
        }
        rewind($f);
        return stream_get_contents($f);
    }

    private function csvRecursos(): string
    {
        $recursos = Recurso::buscarTodosCompletos();
        $f = fopen('php://temp', 'r+');
        fputcsv($f, ["Protocolo Recurso","Protocolo Inscrição","Candidato","Etapa","Motivo","Status","Data Recurso","Data Resposta"]);
        foreach ($recursos as $r) {
            fputcsv($f, [
                $r["protocolo_recurso"],
                $r["protocolo_inscricao"],
                $r["candidato_nome"],
                $r["etapa_nome"],
                $r["motivo"],
                $r["status"],
                date("d/m/Y H:i", strtotime($r["dt_recurso"])),
                !empty($r["dt_resposta"]) ? date("d/m/Y H:i", strtotime($r["dt_resposta"])) : ""
            ]);
        }
        rewind($f);
        return stream_get_contents($f);
    }

    /** ------------ Stream helper (download direto) ------------ */

    private function streamCsv(string $filename, string $csv): void
    {
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Length: " . strlen($csv));
        echo $csv;
        exit;
    }

    /** ------------ Exportar geral em ZIP ------------ */

    private function exportarGeral()
    {
        $zip = new \ZipArchive();
        $filename = "relatorio_geral_" . date("Y-m-d") . ".zip";
        $filepath = sys_get_temp_dir() . "/" . $filename;

        if ($zip->open($filepath, \ZipArchive::CREATE) !== true) {
            header("Location: /admin/relatorios?erro=Erro ao criar arquivo ZIP");
            exit;
        }

        // Adiciona cada relatório
        $zip->addFromString("candidatos_" . date("Y-m-d") . ".csv", $this->csvCandidatos());
        $zip->addFromString("inscricoes_" . date("Y-m-d") . ".csv", $this->csvInscricoes());
        // $zip->addFromString("documentos_" . date("Y-m-d") . ".csv", $this->csvDocumentos());
        $zip->addFromString("recursos_" . date("Y-m-d") . ".csv", $this->csvRecursos());

        $zip->close();

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
        @unlink($filepath);
        exit;
    }

    public function configuracoes()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Implementar salvamento de configurações
            $sucesso = "Configurações salvas com sucesso";
        }

        $this->view("admin/configuracoes", [
            "sucesso" => $sucesso ?? null
        ]);
    }

    public function perfil()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        $mensagem = '';
        $tipo_mensagem = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $nome = trim($_POST['nome'] ?? '');
                $telefone = trim($_POST['telefone'] ?? '');
                $cargo = trim($_POST['cargo'] ?? '');

                if (empty($nome)) {
                    throw new \Exception("Nome é obrigatório");
                }

                // Atualizar dados do administrador
                $db = \App\core\Database::getInstance();
                $stmt = $db->prepare("UPDATE pss.usuarios SET nome = :nome, telefone = :telefone, cargo = :cargo WHERE id = :id");
                $resultado = $stmt->execute([
                    'nome' => $nome,
                    'telefone' => $telefone,
                    'cargo' => $cargo,
                    'id' => $_SESSION['admin_id']
                ]);

                if ($resultado) {
                    $_SESSION['admin_nome'] = $nome;
                    $mensagem = 'Perfil atualizado com sucesso!';
                    $tipo_mensagem = 'sucesso';
                } else {
                    $mensagem = 'Erro ao atualizar perfil. Tente novamente.';
                    $tipo_mensagem = 'erro';
                }

            } catch (\Exception $e) {
                error_log("Erro ao atualizar perfil: " . $e->getMessage());
                $mensagem = $e->getMessage();
                $tipo_mensagem = 'erro';
            }
        }

        // Buscar dados do administrador
        try {
            $db = \App\core\Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM pss.usuarios WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['admin_id']]);
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao buscar dados do admin: " . $e->getMessage());
            $admin = [];
        }

        $this->view("admin/perfil", [
            "admin" => $admin,
            "mensagem" => $mensagem,
            "tipo_mensagem" => $tipo_mensagem
        ]);
    }

    public function alterarSenha()
    {
        if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
            header("Location: /admin/login");
            exit;
        }

        $mensagem = '';
        $tipo_mensagem = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $senha_atual = $_POST['senha_atual'] ?? '';
                $nova_senha = $_POST['nova_senha'] ?? '';
                $confirmar_senha = $_POST['confirmar_senha'] ?? '';

                if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
                    throw new \Exception("Todos os campos são obrigatórios");
                }

                if ($nova_senha !== $confirmar_senha) {
                    throw new \Exception("Nova senha e confirmação não coincidem");
                }

                if (strlen($nova_senha) < 8) {
                    throw new \Exception("Nova senha deve ter pelo menos 8 caracteres");
                }

                // Verificar senha atual
                $db = \App\core\Database::getInstance();
                $stmt = $db->prepare("SELECT senha FROM pss.usuarios WHERE id = :id");
                $stmt->execute(['id' => $_SESSION['admin_id']]);
                $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$admin || !password_verify($senha_atual, $admin['senha'])) {
                    throw new \Exception("Senha atual incorreta");
                }

                // Atualizar senha
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE pss.usuarios SET senha = :senha, dt_ultima_senha = NOW() WHERE id = :id");
                $resultado = $stmt->execute([
                    'senha' => $nova_senha_hash,
                    'id' => $_SESSION['admin_id']
                ]);

                if ($resultado) {
                    $mensagem = 'Senha alterada com sucesso!';
                    $tipo_mensagem = 'sucesso';
                } else {
                    $mensagem = 'Erro ao alterar senha. Tente novamente.';
                    $tipo_mensagem = 'erro';
                }

            } catch (\Exception $e) {
                error_log("Erro ao alterar senha: " . $e->getMessage());
                $mensagem = $e->getMessage();
                $tipo_mensagem = 'erro';
            }
        }

        $this->view("admin/alterar-senha", [
            "mensagem" => $mensagem,
            "tipo_mensagem" => $tipo_mensagem
        ]);
    }
}
