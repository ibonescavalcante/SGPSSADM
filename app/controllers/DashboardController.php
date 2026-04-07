<?php

namespace App\controllers;

use App\models\PssDashboard;
use App\models\PssCargoDashboard;
use App\models\InscricaoDashboard;
use App\models\Relatorio;
use App\models\Usuario;

class DashboardController extends Controller
{
    protected $templates;

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
    }

    public function index()
    {
        $this->view('dashboard/main');
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
        $this->view('dashboard/configuracoes/page');
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
