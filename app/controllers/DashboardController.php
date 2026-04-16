<?php

namespace App\controllers;

use App\middleware\SessionSecurity;
use App\models\PssDashboard;
use App\models\InscricaoDashboard;
use App\models\Candidato;
use App\models\Relatorio;
use App\models\Usuario;

use App\core\Controller;

class DashboardController extends Controller
{
    private $relatorio;


    public function __construct()
    {
        // Chamar o construtor da classe pai para inicializar o Plates
        parent::__construct();
        // $this->templates = new Engine(__DIR__ . '/../views'); // pasta das views

        $this->relatorio = new Relatorio();

        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            header('Location: /dashboard/login');
            exit;
        }
        SessionSecurity::ensureDashboardCsrfToken();
    }

    /**
     * Revalida perfil na BD (não confiar só na sessão).
     */
    private function usuarioLogadoEhAdministrador(): bool
    {
        $uid = (int) ($_SESSION['user']['id'] ?? 0);
        if ($uid < 1) {
            return false;
        }
 
        return Usuario::perfilPorId($uid) === 'administrador';
    }

    /**
     * Respostas JSON de gestão de utilizadores: apenas administrador.
     */
    private function garantirAdministradorPainelApi(): void
    {
        if (!$this->usuarioLogadoEhAdministrador()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Sem permissão.']);
            exit;
        }
    }

    public function index()
    {
        $limiteProcessosHome = 10;
        try {
            $data = [
                'total_processos_em_andamento' => PssDashboard::contarPorStatus('em_andamento'),
                'total_candidatos' => (int) Candidato::contarTotal(),
                'total_inscricoes_ativas' => InscricaoDashboard::contarInscricoesAtivas(),
                'processos_em_andamento' => PssDashboard::buscarPorStatus('em_andamento', $limiteProcessosHome),
            ];
        } catch (\Throwable $e) {
            error_log('Dashboard index: ' . $e->getMessage());
            $data = [
                'total_processos_em_andamento' => 0,
                'total_candidatos' => 0,
                'total_inscricoes_ativas' => 0,
                'processos_em_andamento' => [],
            ];
        }
        $this->view('dashboard/main', $data);
    }

    public function inscricoes()
    {
        $PssDashboard = new PssDashboard();
        $processos = $PssDashboard->buscar_processo_by_status(['em_andamento']);
        $this->view('dashboard/inscricoes/page', ['processos' => $processos]);
    }
    public function recursos()
    {
        $PssDashboard = new PssDashboard();
        // busca  o numro do pss
        $processos = $PssDashboard->buscar_processo_by_status(['em_andamento']);
        $this->view('dashboard/recursos/page', ['processos' => $processos]);
    }
    public function relatorios()
    {


        $PssDashboard = new PssDashboard();
        $processos = $PssDashboard->buscar_processo_by_status(['em_andamento']);
        // echo ("<pre>");
        // var_dump($processos);
        // echo ("</pre>");
        // die;
        $this->view('dashboard/relatorios/page', ['processos' => $processos]);
    }
    public function configuracoes()
    {
        $perfisForm = [
            ['value' => 'comissao', 'label' => 'Comissão avaliadora'],
            ['value' => 'administrador', 'label' => 'Administrador'],
            ['value' => 'visualizador', 'label' => 'Visualizador'],
        ];
        $perfilLabels = [];
        foreach ($perfisForm as $p) {
            $perfilLabels[$p['value']] = $p['label'];
        }
        $uid = (int) ($_SESSION['user']['id'] ?? 0);
        $podeGerirUsuarios = $uid > 0 && Usuario::perfilPorId($uid) === 'administrador';
        if (!$podeGerirUsuarios) {
            $_SESSION['erro'] = 'Acesso negado. Apenas administradores podem aceder a Configurações.';
            header('Location: /dashboard');
            exit;
        }

        $this->view('dashboard/configuracoes/page', [
            'usuarios'            => Usuario::listarParaPainel(),
            'perfis_form'         => $perfisForm,
            'perfil_labels'       => $perfilLabels,
            'pode_gerir_usuarios' => $podeGerirUsuarios,
        ]);
    }

    /**
     * Cria usuário do painel (POST JSON ou form). Exige CSRF.
     */
    public function criar_usuario()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido.']);
            exit;
        }

        if (!SessionSecurity::validarDashboardCsrf()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Token de segurança inválido ou ausente.']);
            exit;
        }

        $this->garantirAdministradorPainelApi();

        $raw = file_get_contents('php://input');
        $input = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        if (!is_array($input)) {
            $input = $_POST;
        }

        $nome = isset($input['nome']) ? trim((string) $input['nome']) : '';
        $email = isset($input['email']) ? strtolower(trim((string) $input['email'])) : '';
        $telefone = isset($input['telefone']) ? trim((string) $input['telefone']) : '';
        $perfil = isset($input['perfil']) ? strtolower(trim((string) $input['perfil'])) : '';
        $senha = isset($input['senha']) ? (string) $input['senha'] : '';
        $senha2 = isset($input['senha_confirmacao']) ? (string) $input['senha_confirmacao'] : '';
        $ativo = !isset($input['ativo']) || $input['ativo'] === true || $input['ativo'] === '1' || $input['ativo'] === 'on';

        if ($nome === '' || mb_strlen($nome) < 2) {
            http_response_code(422);
            echo json_encode(['erro' => 'Informe o nome completo (mínimo 2 caracteres).']);
            exit;
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['erro' => 'E-mail inválido.']);
            exit;
        }

        if (!in_array($perfil, Usuario::PERFIS_VALIDOS, true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Perfil inválido.']);
            exit;
        }

        if (strlen($senha) < 8) {
            http_response_code(422);
            echo json_encode(['erro' => 'A senha deve ter no mínimo 8 caracteres.']);
            exit;
        }

        if (!hash_equals($senha, $senha2)) {
            http_response_code(422);
            echo json_encode(['erro' => 'A confirmação da senha não confere.']);
            exit;
        }

        if (Usuario::emailJaCadastrado($email)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Este e-mail já está cadastrado.']);
            exit;
        }

        try {
            $id = Usuario::criarUsuarioPainel([
                'nome'     => $nome,
                'email'    => $email,
                'telefone' => $telefone,
                'perfil'   => $perfil,
                'senha'    => $senha,
                'ativo'    => $ativo,
            ]);
        } catch (\PDOException $e) {
            error_log('criar_usuario PDO: ' . $e->getMessage());
            $sqlState = $e->errorInfo[0] ?? '';
            $msg = strtolower($e->getMessage());
            if ($sqlState === '23505' || str_contains($msg, '23505')) {
                http_response_code(422);
                echo json_encode(['erro' => Usuario::mensagemErroDuplicacao($e)]);
                exit;
            }
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao salvar no banco de dados. Verifique os dados ou o tipo perfil no PostgreSQL.']);
            exit;
        } catch (\Throwable $e) {
            error_log('criar_usuario: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno ao criar usuário.']);
            exit;
        }

        if ($id === false) {
            http_response_code(500);
            echo json_encode(['erro' => 'Não foi possível criar o usuário.']);
            exit;
        }

        echo json_encode(['ok' => true, 'id' => $id, 'mensagem' => 'Usuário criado com sucesso.']);
        exit;
    }

    /**
     * Atualiza utilizador do painel (POST JSON). Exige CSRF e perfil administrador.
     */
    public function atualizar_usuario()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido.']);
            exit;
        }

        if (!SessionSecurity::validarDashboardCsrf()) {
            http_response_code(403);
            echo json_encode(['erro' => 'Token de segurança inválido ou ausente.']);
            exit;
        }

        $this->garantirAdministradorPainelApi();

        $raw = file_get_contents('php://input');
        $input = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        if (!is_array($input)) {
            $input = $_POST;
        }

        $id = isset($input['id']) ? (int) $input['id'] : 0;
        if ($id < 1) {
            http_response_code(422);
            echo json_encode(['erro' => 'Identificador do utilizador inválido.']);
            exit;
        }

        if (Usuario::buscarParaEdicaoPainel($id) === null) {
            http_response_code(404);
            echo json_encode(['erro' => 'Utilizador não encontrado.']);
            exit;
        }

        $nome = isset($input['nome']) ? trim((string) $input['nome']) : '';
        $email = isset($input['email']) ? strtolower(trim((string) $input['email'])) : '';
        $telefone = isset($input['telefone']) ? trim((string) $input['telefone']) : '';
        $perfil = isset($input['perfil']) ? strtolower(trim((string) $input['perfil'])) : '';
        $senha = isset($input['senha']) ? (string) $input['senha'] : '';
        $senha2 = isset($input['senha_confirmacao']) ? (string) $input['senha_confirmacao'] : '';
        if (!array_key_exists('ativo', $input)) {
            $ativo = true;
        } else {
            $av = $input['ativo'];
            $ativo = $av === true || $av === 1 || $av === '1' || $av === 'on' || $av === 'true';
        }

        if ($nome === '' || mb_strlen($nome) < 2) {
            http_response_code(422);
            echo json_encode(['erro' => 'Informe o nome completo (mínimo 2 caracteres).']);
            exit;
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['erro' => 'E-mail inválido.']);
            exit;
        }

        if (!in_array($perfil, Usuario::PERFIS_VALIDOS, true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Perfil inválido.']);
            exit;
        }

        if ($senha !== '' || $senha2 !== '') {
            if (strlen($senha) < 8) {
                http_response_code(422);
                echo json_encode(['erro' => 'A senha deve ter no mínimo 8 caracteres.']);
                exit;
            }
            if (!hash_equals($senha, $senha2)) {
                http_response_code(422);
                echo json_encode(['erro' => 'A confirmação da senha não confere.']);
                exit;
            }
        }

        if (Usuario::emailJaCadastrado($email, $id)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Este e-mail já está cadastrado.']);
            exit;
        }

        try {
            $ok = Usuario::atualizarUsuarioPainel($id, [
                'nome'                => $nome,
                'email'               => $email,
                'telefone'            => $telefone,
                'perfil'              => $perfil,
                'ativo'               => $ativo,
                'senha'               => $senha,
                'senha_confirmacao'   => $senha2,
            ]);
        } catch (\PDOException $e) {
            error_log('atualizar_usuario PDO: ' . $e->getMessage());
            $sqlState = $e->errorInfo[0] ?? '';
            $msg = strtolower($e->getMessage());
            if ($sqlState === '23505' || str_contains($msg, '23505')) {
                http_response_code(422);
                echo json_encode(['erro' => Usuario::mensagemErroDuplicacao($e)]);
                exit;
            }
            if ($sqlState === '22P02' || str_contains($msg, 'invalid input value for enum')
                || str_contains($msg, 'invalid input syntax for type')) {
                http_response_code(422);
                echo json_encode(['erro' => 'Valor inválido para um campo na base de dados (ex.: perfil ou formato de dado).']);
                exit;
            }
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao salvar no banco de dados.']);
            exit;
        } catch (\Throwable $e) {
            error_log('atualizar_usuario: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno ao atualizar utilizador.']);
            exit;
        }

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['erro' => 'Não foi possível atualizar o utilizador.']);
            exit;
        }

        echo json_encode(['ok' => true, 'mensagem' => 'Utilizador atualizado com sucesso.']);
        exit;
    }

    public function processos()
    {
        $this->view('dashboard/processos/page');
    }
    public function processos_novo()
    {
        $this->view('dashboard/processos/novo/page');
    }
    public function detalhes($id)
    {

        $InscricaoDashboard = new InscricaoDashboard();
        $Detalhe = $InscricaoDashboard->busca_inscricoes_by_id($id);
        $pontuacao = $InscricaoDashboard->busca_pontuacao_inscricao($id);
        $avaliador = $InscricaoDashboard->getAvaliador_Inscricao($id);
        // var_dump($avaliador);
        if ($avaliador) {
            $avaliadornome = new Usuario();
            $avaliador = $avaliadornome->buscarNomePorId($avaliador);
            $avaliador = $avaliador['nome'] ?? 'Não avaliado';
            // echo "<br> Avaliador: " . $avaliador;
        } else {
            $avaliador = $pontuacao->user_name ?? 'Não avaliado';
        }
        // echo "<br> Avaliador: " . $avaliador;
        // die;
        // echo ("<pre>");

        $this->view('dashboard/inscricoes/detalhes', ['detalhes' => $Detalhe, 'pontuacao' => $pontuacao, 'avaliador' => $avaliador]);
    }
    public function detalhes_recursos($id_recurso)
    {
        $recursoDashboard = new InscricaoDashboard();
        $recurso = $recursoDashboard->busca_recursos_detalhes($id_recurso);
        $this->view('dashboard/recursos/detalhes', ['detalhes' => $recurso]);
    }

    public function relatorios_api()
    {
        header('Content-Type: application/json');

        // Pega os dados do POST
        $filtros = json_decode(file_get_contents('php://input'), true) ?? [];

        echo $filtros;
        die;

        $relatorioModel = new Relatorio();

        // Busca os dados filtrados
        $relatorios = $relatorioModel->buscarRelatorios($filtros);

        // Por enquanto, os dados dos gráficos continuam simulados
        $dadosGraficos = [
            'tipos' => $relatorioModel->buscarDadosGraficoTipos($filtros),
            'downloads' => $relatorioModel->buscarDadosGraficoDownloads($filtros)
        ];

        // Formata os dados dos relatórios para o frontend
        $relatoriosFormatados = array_map(function ($rel) {
            return [
                'id' => $rel['id'],
                'titulo' => 'Inscrição de ' . htmlspecialchars($rel['nome_candidato']),
                'descricao' => 'Relatório de inscrição para o cargo de ' . htmlspecialchars($rel['nome_cargo']) . ' no processo ' . htmlspecialchars($rel['titulo_processo']) . '.',
                'tipo' => ucfirst(htmlspecialchars($rel['status'])),
                'formato' => 'PDF', // Fixo por enquanto
                'atualizado_em' => date('d/m/Y', strtotime($rel['data_inscricao'])),
                'downloads' => 0 // Dado simulado
            ];
        }, $relatorios);


        echo json_encode([
            'relatorios' => $relatoriosFormatados,
            'graficos' => $dadosGraficos,
            'total' => count($relatoriosFormatados)
        ]);
        exit;
    }

    public function gerarRelatorio()
    {
        $filtros = $_GET; // Pega os filtros da URL
        // var_dump($filtros);
        // die;

        $formato = $filtros['formato'] ?? 'pdf'; // Padrão para PDF
        $tipo_relatorio = $filtros['tipo_relatorio'];

        // $relatorioModel = new Relatorio();
        // $dados = $relatorioModel->buscarRelatorios($filtros);
        // $dados = $this->relatorio->buscarRelatorios($filtros);

        // var_dump($dados);
        // die;

        // if (empty($dados)) {
        //     $_SESSION['erro'] = " sem dados";
        //     header("Location: /dashboard/relatorios");
        //     return;
        //     exit;
        // }
        switch ($formato) {
            case 'pdf':

                if ($tipo_relatorio === '5')
                    $this->relatorio->resultado_preliminar_deferidas_pcd_PDF($filtros);
                if ($tipo_relatorio === '7')
                    $this->relatorio->resultado_preliminar_deferidas_ampla_PDF($filtros);
                if ($tipo_relatorio === '6')
                    $this->relatorio->resultado_preliminar_análise_curricular($filtros);
                if ($tipo_relatorio === '2')
                    $this->relatorio->resultado_definitivo_análise_curricular($filtros);
                if ($tipo_relatorio === '8')
                    $this->relatorio->homologacao_resultado_classificatorio($filtros);
                break;
            case 'csv':
                // $this->_gerarCsv($dados);
                break;
            default:
                die("Formato de relatório não suportado.");
        }
    }

    // private function _gerarPdf1111($dados)
    // {
    //     echo ("<pre>");
    //     var_dump($dados);
    //     // var_dump(array_keys($dados[0]));
    //     echo ("<pre>");
    //     die;
    //     // Usa a classe CustomPDF em vez de TCPDF
    //     $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    //     $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    //     // Informações do documento
    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetTitle('PROCESSO SELETIVO SIMPLIFICADO (PSS) 0 EDITAL N.º 01/2025 0 1º PSS/PMP');
    //     $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    //     $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //     $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //     $pdf->SetHeaderMargin(PDF_MARGIN_HEADER); // Necessário para o cabeçalho personalizado
    //     $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //     $pdf->AddPage();
    //     $pdf->Ln(16);
    //     // Cabeçalho da tabela
    //     $pdf->SetFont('helvetica', 'B', 10);

    //     $header = array_keys($dados[0]); // Pega as chaves do primeiro registro
    //     $colCount = count($header);
    //     // Define larguras automáticas (distribui proporcionalmente)
    //     $pageWidth = $pdf->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;
    //     $w = array_fill(0, $colCount, $pageWidth / $colCount);
    //     // $header = ['Inscrição', 'Nome',  'Cargo', 'Status'];
    //     // $w = [35, 70,  60, 15]; // Larguras das colunas ajustadas


    //     // Estilo do cabeçalho: Fundo preto e texto branco
    //     $pdf->SetFillColor(0, 0, 0);
    //     $pdf->SetTextColor(255, 255, 255);
    //     for ($i = 0; $i < count($header); $i++) {
    //         $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    //     }
    //     $pdf->Ln();

    //     // Dados da tabela
    //     $pdf->SetFont('helvetica', '', 8);
    //     // Restaura a cor do texto para preto para os dados
    //     $pdf->SetTextColor(50, 50, 50);
    //     $pdf->SetFillColor(245, 245, 245);
    //     $fill = false;
    //     foreach ($dados as $row) {
    //         // $pdf->Cell('$w[$i]', 0, "tese", 'LR', 0, 'L', $fill);
    //         foreach ($header as $i => $col) {
    //             $valor = isset($row[$col]) ? $row[$col] : '';
    //             $valor = is_string($valor) ? ucfirst(strtolower($valor)) : $valor;
    //             $pdf->Cell($w[$i], 0, $valor, 0, 0, 'L', $fill);
    //         }
    //         $pdf->Ln();
    //         $fill = !$fill;
    //     }
    //     // foreach ($dados as $row) {
    //     //     $pdf->Cell($w[0], 6, $row['protocolo'], 'LR', 0, 'L', $fill);
    //     //     $pdf->Cell($w[1], 6,  ucfirst(strtolower($row['candidato_nome'])), 'LR', 0, 'L', $fill);
    //     //     $pdf->Cell($w[2], 6,  ucfirst(strtolower($row['cargo_nome'])), 'LR', 0, 'L', $fill);
    //     //     $pdf->Cell($w[3], 6, $row['status'], 'LR', 0, 'L', $fill);
    //     //     $pdf->Ln();
    //     //     $fill = !$fill;
    //     // }
    //     $pdf->Cell(array_sum($w), 0, '', 'T');

    //     // Saída do PDF
    //     $pdf->Output('relatorio_inscricoes.pdf', 'I'); // 'I' para inline, 'D' para download
    //     exit;
    // }
    // private function _gerarPdf($dados)
    // {
    //     if (empty($dados)) {
    //         die('Nenhum dado disponível para gerar o relatório.');
    //     }

    //     // var_dump($dados);
    //     // die;

    //     // Agrupa os dados por cargo_concatenado
    //     $agrupado = [];
    //     foreach ($dados as $item) {
    //         $cargo = $item['cargo_concatenado'];
    //         if (!isset($agrupado[$cargo])) {
    //             $agrupado[$cargo] = [];
    //         }
    //         $agrupado[$cargo][] = $item;
    //     }

    //     // Inicializa o PDF
    //     $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


    //     // 🔹 Define dinamicamente os textos do cabeçalho
    //     $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
    //     $pdf->subtitulo = 'PROCESSO SELETIVO SIMPLIFICADO (PSS) - EDITAL Nº 01/2025';
    //     $pdf->descricao = 'RESULTADO PRELIMINAR DE INSCRIÇÕES DEFERIDAS';

    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetTitle($pdf->descricao);
    //     $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //     $pdf->AddPage();

    //     $pdf->SetFont('helvetica', '', 10);

    //     // Larguras das colunas fixas
    //     $w = [40, 70, 30, 40]; // CPF, Nome, Resultado, Modalidade
    //     $pdf->Ln(6);
    //     $count = 0;
    //     foreach ($agrupado as $cargo => $candidatos) {
    //         // Título do cargo
    //         $pdf->SetFont('helvetica', 'B', 11);
    //         $pdf->SetTextColor(0, 0, 0);
    //         $pdf->Ln(6);
    //         $pdf->MultiCell(0, 4, strtoupper($cargo), 0, 'L');
    //         $pdf->Ln(4);

    //         // Cabeçalho das colunas
    //         if ($count == 0) {
    //             $pdf->SetFont('helvetica', 'B', 9);
    //             $pdf->SetTextColor(60, 60, 60);
    //             $pdf->SetFillColor(230, 230, 230);
    //             $pdf->Cell($w[0], 7, 'CPF', 0, 0, 'L', true);
    //             $pdf->Cell($w[1], 7, 'Nome do Candidato', 0, 0, 'L', true);
    //             $pdf->Cell($w[2], 7, 'Resultado', 0, 0, 'L', true);
    //             $pdf->Cell($w[3], 7, 'Modalidade', 0, 0, 'L', true);
    //         }
    //         $pdf->Ln(6);

    //         // Dados dos candidatos
    //         $pdf->SetFont('helvetica', '', 9);
    //         $pdf->SetTextColor(30, 30, 30);
    //         $pdf->SetFillColor(245, 245, 245);
    //         $fill = false;
    //         $count++;
    //         foreach ($candidatos as $c) {
    //             $pdf->Cell($w[0], 6, $c['cpf_candidato'], 0, 0, 'L', $fill);
    //             $pdf->Cell($w[1], 6, ucfirst(strtolower($c['nome_candidato'])), 0, 0, 'L', $fill);
    //             $pdf->Cell($w[2], 6, $c['resultado'], 0, 0, 'L', $fill);
    //             $pdf->Cell($w[3], 6, $c['modalidade'], 0, 0, 'L', $fill);
    //             $pdf->Ln(6);
    //             $fill = !$fill;
    //         }

    //         // Linha separadora entre cargos
    //         $pdf->Ln(6);
    //         $pdf->SetDrawColor(200, 200, 200);
    //         $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
    //         $pdf->Ln(4);
    //     }
    //     // Parâmetro	Ação
    //     // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
    //     // 'D'	Download direto — o navegador baixa o arquivo
    //     // 'F'	Salva o arquivo em um caminho local do servidor
    //     // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
    //     $pdf->Output('relatorio_agrupado.pdf', 'D');
    //     exit;
    // }




    // private function _gerarCsv($dados)
    // {
    //     header('Content-Type: text/csv');
    //     header('Content-Disposition: attachment; filename="relatorio_inscricoes.csv"');

    //     $output = fopen('php://output', 'w');

    //     // Cabeçalho
    //     fputcsv($output, ['protocolo', 'candidato_nome', 'candidato_cpf', 'pss_titulo', 'cargo_nome', 'status']);

    //     // Dados
    //     foreach ($dados as $row) {
    //         fputcsv($output, $row);
    //     }

    //     fclose($output);
    //     exit;
    // }
}
