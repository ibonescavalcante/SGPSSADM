<?php

namespace App\models;

use App\core\Database;
use PDO;
use Exception;
use TCPDF;

//COSTOMIZAÇÃO DO HEADER DO GERADOR DE PDF
class CustomPDF extends TCPDF
{
    public $tituloPrincipal = '';
    public $subtitulo = '';
    public $descricao = '';
    public $legenda = '';
    public $legenda3 = '';
    public $legenda1 = '';
    public $legenda2 = '';
    public $legenda4 = '';

    public function Header()
    {

        if ($this->getPage() == 1) {
            $brasaoPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/brasao.png';
            $pageWidth = $this->getPageWidth();

            // Logo centralizado
            if (file_exists($brasaoPath)) {
                $logoWidth = 25;
                $x = ($pageWidth - $logoWidth) / 2;
                $this->Image($brasaoPath, $x, 0, $logoWidth, '', 'PNG');
                $this->Ln(22);
            }

            // Texto principal
            $this->SetFont('helvetica', 'B', 10);
            $this->Cell(0, 3, $this->tituloPrincipal ?: 'PREFEITURA MUNICIPAL DE PARAUAPEBAS', 0, 1, 'C');

            // Subtítulo
            if ($this->subtitulo) {
                $this->SetFont('helvetica', '', 9);
                $this->MultiCell(0, 6, $this->subtitulo, 0, 'C');
            }

            // Descrição
            if ($this->descricao) {
                $this->SetFont('helvetica', '', 9);
                $this->MultiCell(0, 8, $this->descricao, 0, 'L ');
            }
            if ($this->legenda3) {
                $this->Ln(1);
                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(0, 5, $this->legenda3, 0, 'L');
            }
            if ($this->legenda) {
                $this->Ln(1);
                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(0, 5, $this->legenda, 0, 'L');
            }
            if ($this->legenda1) {

                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(0, 5, $this->legenda1, 0, 'L');
            }
            if ($this->legenda2) {

                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(0, 5, $this->legenda2, 0, 'L');
            }
            if ($this->legenda4) {

                $this->SetFont('helvetica', 'B', 9);
                $this->MultiCell(0, 5, $this->legenda4, 0, 'L');
            }
        }
    }
}

class Relatorio
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get_dados(array $filtros = [])
    {
        $pss_id = $filtros['processo_id'];
        $relatorio_tipo = $filtros['tipo_relatorio'] ?? null;

        if ($relatorio_tipo == 6) {
            $sql = "SELECT * FROM pss.fn_rel_resultado_preliminar_classificatorio(" . $pss_id . ");";
        }
        if ($relatorio_tipo == 7 || $relatorio_tipo == 5) {
            $sql = "SELECT * FROM pss.fn_rel_resultado_preliminar_inscricoes_deferidas(" . $pss_id . ");";
        }
        if ($relatorio_tipo == 2) {
            $sql = "SELECT * FROM pss.fn_rel_homolog_result_class_final(" . $pss_id . ");";
        }
        if ($relatorio_tipo == 8) {
            $sql = "SELECT * FROM pss.fn_rel_homolog_result_class_final(" . $pss_id . ");";
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
            header("Location: /dashboard/relatorios");
            return;
        }
    }

    public function resultado_preliminar_deferidas_pcd_PDF($filtros)
    {

        // buscar pss por id
        $pss_nome = Pss::buscarPorId($filtros['processo_id'])['titulo'];
        // echo ($pss_nome);
        // die;
        $dados = $this->get_dados($filtros);

        if (empty($dados)) {
            die('Nenhum dado disponível para gerar o relatório.');
        }

        // Agrupa os dados por cargo_concatenado
        $agrupado = [];
        foreach ($dados as $item) {
            $cargo = $item['cargo_concatenado'];
            if (!isset($agrupado[$cargo])) {
                $agrupado[$cargo] = [];
            }
            $agrupado[$cargo][] = $item;
        }
        // Inicializa o PDF
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 🔹 Define dinamicamente os textos do cabeçalho
        $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
        $pdf->subtitulo = $pss_nome;
        $pdf->descricao = "O Prefeito Municipal de Parauapebas, no uso de suas atribuições legais, torna público o resultado preliminar de candidatos com inscrição deferida, que concorrerão na condição de PcD, para participar do Processo Seletivo Simplificado destinado à contratação por prazo determinado para as funções de Nível Elementar e Auxiliar, correspondentes aos cargos do quadro de pessoal da Prefeitura, conforme a Lei Municipal nº 4.249, de 17 de dezembro de 2002, mediante as condições estabelecidas neste Edital.";

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($pdf->descricao);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        // Larguras das colunas fixas
        $w = [40, 70, 30, 40]; // CPF, Nome, Resultado, Modalidade
        $pdf->Ln(28);
        $count = 0;
        foreach ($agrupado as $cargo => $candidatos) {
            // Título do cargo
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(0, 2, strtoupper($cargo), 0, 'L');
            // $pdf->Ln(4);

            // Cabeçalho das colunas
            if ($count == 0) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell($w[0], 7, 'CPF', 0, 0, 'L', true);
                $pdf->Cell($w[1], 7, 'Nome do Candidato', 0, 0, 'L', true);
                $pdf->Cell($w[2], 7, 'Resultado', 0, 0, 'L', true);
                $pdf->Cell($w[3], 7, 'Modalidade', 0, 0, 'L', true);
            }
            $pdf->Ln(6);

            // Dados dos candidatos
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetFillColor(245, 245, 245);
            $fill = false;
            $count++;
            foreach ($candidatos as $c) {
                if ($c['modalidade'] != 'Ampla Concorrência') {
                    $pdf->Cell($w[0], 6, $c['cpf_candidato'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[1], 6, $c['nome_candidato'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[2], 6, $c['resultado'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[3], 6, $c['modalidade'], 0, 0, 'L', $fill);
                    $pdf->Ln(6);
                    $fill = !$fill;
                }
            }

            // Linha separadora entre cargos
            $pdf->Ln(6);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
            $pdf->Ln(4);
        }
        // Parâmetro	Ação
        // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
        // 'D'	Download direto — o navegador baixa o arquivo
        // 'F'	Salva o arquivo em um caminho local do servidor
        // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
        $pdf->Output('resultado_preliminar_deferidas_pdc.pdf', 'D');
        exit;
    }
    public function resultado_preliminar_análise_curricular($filtros)
    {
        $pss_nome = Pss::buscarPorId($filtros['processo_id'])['titulo'];
        $dados = $this->get_dados($filtros);
        // echo ("<pre>");
        // var_dump($dados);
        // echo ("</pre>");
        // die;

        if (empty($dados)) {
            die('Nenhum dado disponível para gerar o relatório.');
        }

        // Agrupa os dados por cargo_concatenado
        $agrupado = [];
        foreach ($dados as $item) {
            $cargo = $item['cargo_concatenado'];
            if (!isset($agrupado[$cargo])) {
                $agrupado[$cargo] = [];
            }
            $agrupado[$cargo][] = $item;
        }
        // Inicializa o PDF
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 🔹 Define dinamicamente os textos do cabeçalho
        $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
        $pdf->subtitulo = $pss_nome;
        $pdf->descricao = "O Prefeito Municipal de Parauapebas, no uso de suas atribuições legais, torna público o resultado preliminar de análise curricular para participar do Processo Seletivo Simplificado destinado à contratação por prazo determinado para
                            as funções de Nível Elementar e Auxiliar, correspondentes aos cargos do quadro de pessoal da Prefeitura, conforme a Lei
                            Municipal nº 4.249, de 17 de dezembro de 2002, mediante as condições estabelecidas neste Edital.";

        $pdf->legenda3 = "Legenda dos Itens de Pontuação para Análise Curricular";
        $pdf->legenda = "Item 1 - Escolaridade";
        $pdf->legenda1 = "Item 2 - Cursos de Aperfeiçoamento";
        $pdf->legenda2 = "Item 3 - Experiência";

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($pdf->descricao);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        // Larguras das colunas fixas
        $w = [10, 20, 10, 70, 20, 20, 20, 10];
        // Ord, CPF, PcD, NOME, Item 1, Item 2, Item 3, Total
        // $w = [40, 70, 30, 40, 0, 0, 0, 0]; // Ord, CPF, PcD, NOME, Item 1, Item 2, Item 3, Total
        $pdf->Ln(38);
        $count = 0;
        foreach ($agrupado as $cargo => $candidatos) {
            // Título do cargo
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 4, strtoupper($cargo), 0, 'L');
            $pdf->Ln(4);

            // Cabeçalho das colunas
            if ($count == 0) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell($w[0], 7, 'Ord', 0, 0, 'L', true);
                $pdf->Cell($w[1], 7, 'CPF', 0, 0, 'L', true);
                $pdf->Cell($w[2], 7, 'Pcd', 0, 0, 'L', true);
                $pdf->Cell($w[3], 7, 'NOME', 0, 0, 'L', true);
                $pdf->Cell($w[4], 7, 'Item 1', 0, 0, 'L', true);
                $pdf->Cell($w[5], 7, 'Item 2', 0, 0, 'L', true);
                $pdf->Cell($w[6], 7, 'Item 3', 0, 0, 'L', true);
                $pdf->Cell($w[7], 7, 'Total', 0, 0, 'L', true);
            }
            $pdf->Ln(6);

            // Dados dos candidatos
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetFillColor(245, 245, 245);
            $fill = false;
            $count++;
            foreach ($candidatos as $index => $c) {
                $pdf->Cell($w[0], 6, $index + 1, 0, 0, 'L', $fill);
                $pdf->Cell($w[1], 6, $c['cpf_candidato'], 0, 0, 'L', $fill);
                $pdf->Cell($w[2], 6, $c['PcD'], 0, 0, 'L', $fill);
                $pdf->Cell($w[3], 6, htmlspecialchars($c['nome_candidato']), 0, 0, 'L', $fill);
                // $pdf->Cell($w[4], 6, $c['Item 1 - Escolaridade'], 0, 0, 'L', $fill);
                $pdf->Cell($w[4], 6, number_format($c['Item 1 - Escolaridade'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[5], 6, number_format($c['Item 2 - Cursos de Aperfeiçoamento'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[6], 6, number_format($c['Item 3 - Experiência'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[7], 6, number_format($c['total'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Ln(6);
                $fill = !$fill;
            }

            // Linha separadora entre cargos
            $pdf->Ln(6);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
            $pdf->Ln(4);
        }
        // Parâmetro	Ação
        // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
        // 'D'	Download direto — o navegador baixa o arquivo
        // 'F'	Salva o arquivo em um caminho local do servidor
        // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
        $pdf->Output('resultado_preliminar_análise_curricular.pdf', 'D');
        exit;
    }
    public function resultado_definitivo_análise_curricular($filtros)
    {
        $pss_nome = Pss::buscarPorId($filtros['processo_id'])['titulo'];
        $dados = $this->get_dados($filtros);
        // echo ("<pre>");
        // var_dump($dados);
        // echo ("</pre>");
        // die;

        if (empty($dados)) {
            die('Nenhum dado disponível para gerar o relatório.');
        }

        // Agrupa os dados por cargo_concatenado
        $agrupado = [];
        foreach ($dados as $item) {
            $cargo = $item['CARGO'];
            if (!isset($agrupado[$cargo])) {
                $agrupado[$cargo] = [];
            }
            $agrupado[$cargo][] = $item;
        }
        // Inicializa o PDF
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 🔹 Define dinamicamente os textos do cabeçalho
        $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
        $pdf->subtitulo = $pss_nome;
        $pdf->descricao = "O Prefeito Municipal de Parauapebas, no uso de suas atribuições legais, torna público o Resultado Definitivo do Processo de Análise Curricular e Classificatório para participar do Processo Seletivo Simplificado destinado à contratação por prazo determinado para as funções de Nível Elementar e Auxiliar, correspondentes aos cargos do quadro de pessoal da Prefeitura, conforme a Lei Municipal nº 4.249, de 17 de dezembro de 2002, mediante as condições estabelecidas neste Edital.";

        $pdf->legenda3 = "Legenda dos itens pontuados";
        $pdf->legenda = "Escolaridade: ES";
        $pdf->legenda1 = "Cursos de aprimoramento: CA";
        $pdf->legenda2 = "Tempo de serviço e atividade no Setor Publico e Privado, na função que que concorre: TSPP";
        // $pdf->legenda4 = "Tempo de serviço na Administração Pública Direta ou Indireta, na função que concorre: TSAP";

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($pdf->descricao);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        // Larguras das colunas fixas
        $w = [8, 16, 8, 70, 16, 9, 9, 9, 15, 20];

        $pdf->Ln(44);
        $count = 0;
        foreach ($agrupado as $cargo => $candidatos) {
            // Título do cargo
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 4, strtoupper($cargo), 0, 'L');
            $pdf->Ln(4);

            // Cabeçalho das colunas
            if ($count == 0) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell($w[0], 7, 'Ord', 0, 0, 'L', true);
                $pdf->Cell($w[1], 7, 'CPF   ', 0, 0, 'L', true);
                $pdf->Cell($w[2], 7, 'PcD', 0, 0, 'L', true);
                $pdf->Cell($w[3], 7, 'NOME', 0, 0, 'L', true);
                $pdf->Cell($w[4], 7, 'NASC.', 0, 0, 'L', true);
                $pdf->Cell($w[5], 7, 'ES', 0, 0, 'L', true);
                $pdf->Cell($w[6], 7, 'CA', 0, 0, 'L', true);
                $pdf->Cell($w[7], 7, 'TSPP', 0, 0, 'L', true);
                $pdf->Cell($w[8], 7, 'TOTAL', 0, 0, 'L', true);
                $pdf->Cell($w[9], 7, 'SITUAÇÃO', 0, 0, 'L', true);
            }
            $pdf->Ln(6);

            // Dados dos candidatos
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetFillColor(245, 245, 245);
            $fill = false;
            $count++;
            foreach ($candidatos as $index => $c) {
                $pdf->Cell($w[0], 6, $index + 1, 0, 0, 'L', $fill);
                $pdf->Cell($w[1], 6, $c['CPF'], 0, 0, 'L', $fill);
                $pdf->Cell($w[2], 6, $c['PcD'], 0, 0, 'L', $fill);
                $pdf->Cell($w[3], 6, htmlspecialchars($c['NOME']), 0, 0, 'L', $fill);
                $pdf->Cell($w[4], 6, htmlspecialchars($c['NASC.']), 0, 0, 'L', $fill);

                $pdf->Cell($w[5], 6, number_format($c['ES'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[6], 6, number_format($c['CA'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[7], 6, number_format($c['TSPP'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[8], 6, number_format($c['TOTAL'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Cell($w[9], 6, $c['SITUAÇÃO'], 0, 0, 'L', $fill);
                $pdf->Ln(6);
                $fill = !$fill;
            }

            // Linha separadora entre cargos
            $pdf->Ln(6);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
            $pdf->Ln(4);
        }
        // Parâmetro	Ação
        // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
        // 'D'	Download direto — o navegador baixa o arquivo
        // 'F'	Salva o arquivo em um caminho local do servidor
        // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
        $pdf->Output('resultado_definitivo_análise_curricular.pdf', 'D');
        exit;
    }
    public function homologacao_resultado_classificatorio($filtros)
    {
        $pss_nome = Pss::buscarPorId($filtros['processo_id'])['titulo'];
        $dados = $this->get_dados($filtros);
        echo ("<pre>");
        var_dump($dados);
        echo ("</pre>");
        die;

        if (empty($dados)) {
            die('Nenhum dado disponível para gerar o relatório.');
        }

        // Agrupa os dados por cargo_concatenado
        $agrupado = [];
        foreach ($dados as $item) {
            $cargo = $item['cargo_concatenado'];
            if (!isset($agrupado[$cargo])) {
                $agrupado[$cargo] = [];
            }
            $agrupado[$cargo][] = $item;
        }
        // Inicializa o PDF
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 🔹 Define dinamicamente os textos do cabeçalho
        $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
        $pdf->subtitulo = $pss_nome;
        $pdf->descricao = "O Prefeito Municipal de Parauapebas, no uso de suas atribuições legais, torna público a HOMOLOGAÇÃO DO RESULTADO FINAL DEFINITIVO do Processo Seletivo Simplificado destinado à contratação por prazo determinado para as funções de Nível Elementar e Auxiliar, correspondentes aos cargos do quadro de pessoal da Prefeitura, conforme a Lei Municipal nº 4.249, de 17 de dezembro de 2002, mediante as condições estabelecidas neste Edital.";

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($pdf->descricao);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        // Larguras das colunas fixas
        $w = [20, 20, 70, 20, 20, 20];

        $pdf->Ln(38);
        $count = 0;
        foreach ($agrupado as $cargo => $candidatos) {
            // Título do cargo
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 4, strtoupper($cargo), 0, 'L');
            $pdf->Ln(4);

            // Cabeçalho das colunas
            if ($count == 0) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell($w[0], 7, 'INSCRIÇÃO', 0, 0, 'L', true);
                $pdf->Cell($w[1], 7, 'PcD', 0, 0, 'L', true);
                $pdf->Cell($w[2], 7, 'NOME', 0, 0, 'L', true);
                $pdf->Cell($w[3], 7, 'NASC.', 0, 0, 'L', true);
                $pdf->Cell($w[4], 7, 'TOTAL', 0, 0, 'L', true);
                $pdf->Cell($w[5], 7, 'SITUAÇÃO', 0, 0, 'L', true);
                // $pdf->Cell($w[7], 7, 'Total', 0, 0, 'L', true);
            }
            $pdf->Ln(6);

            // Dados dos candidatos
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetFillColor(245, 245, 245);
            $fill = false;
            $count++;
            foreach ($candidatos as $index => $c) {
                // $pdf->Cell($w[0], 6, $index + 1, 0, 0, 'L', $fill);
                // $pdf->Cell($w[1], 6, $c['cpf_candidato'], 0, 0, 'L', $fill);
                // $pdf->Cell($w[2], 6, $c['PcD'], 0, 0, 'L', $fill);
                // $pdf->Cell($w[3], 6, htmlspecialchars($c['nome_candidato']), 0, 0, 'L', $fill);      
                // $pdf->Cell($w[4], 6, number_format($c['Item 1 - Escolaridade'], 2, ',', ''), 0, 0, 'L', $fill);
                // $pdf->Cell($w[5], 6, number_format($c['Item 2 - Cursos de Aperfeiçoamento'], 2, ',', ''), 0, 0, 'L', $fill);
                // $pdf->Cell($w[6], 6, number_format($c['Item 3 - Experiência'], 2, ',', ''), 0, 0, 'L', $fill);
                // $pdf->Cell($w[7], 6, number_format($c['total'], 2, ',', ''), 0, 0, 'L', $fill);
                $pdf->Ln(6);
                $fill = !$fill;
            }

            // Linha separadora entre cargos
            $pdf->Ln(6);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
            $pdf->Ln(4);
        }
        // Parâmetro	Ação
        // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
        // 'D'	Download direto — o navegador baixa o arquivo
        // 'F'	Salva o arquivo em um caminho local do servidor
        // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
        $pdf->Output('resultado_preliminar_análise_curricular.pdf', 'I');
        exit;
    }
    public function resultado_preliminar_deferidas_ampla_PDF($filtros)
    {
        $pss_nome = Pss::buscarPorId($filtros['processo_id'])['titulo'];
        $dados = $this->get_dados($filtros);

        if (empty($dados)) {
            die('Nenhum dado disponível para gerar o relatório.');
        }

        // Agrupa os dados por cargo_concatenado
        $agrupado = [];
        foreach ($dados as $item) {
            $cargo = $item['cargo_concatenado'];
            if (!isset($agrupado[$cargo])) {
                $agrupado[$cargo] = [];
            }
            $agrupado[$cargo][] = $item;
        }
        // Inicializa o PDF
        $pdf = new CustomPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 🔹 Define dinamicamente os textos do cabeçalho
        $pdf->tituloPrincipal = 'PREFEITURA MUNICIPAL DE PARAUAPEBAS';
        $pdf->subtitulo = $pss_nome;
        $pdf->descricao = "O Prefeito Municipal de Parauapebas, no uso de suas atribuições legais, torna público o resultado preliminar de candidatos com inscrição deferida para participar do Processo Seletivo Simplificado destinado à contratação por prazo determinado para as funções de Nível Elementar e Auxiliar, correspondentes aos cargos do quadro de pessoal da Prefeitura, conforme a Lei Municipal nº 4.249, de 17 de dezembro de 2002, mediante as condições estabelecidas neste Edital.";

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($pdf->descricao);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        // Larguras das colunas fixas
        $w = [40, 70, 30, 40]; // CPF, Nome, Resultado, Modalidade
        $pdf->Ln(20);
        $count = 0;
        foreach ($agrupado as $cargo => $candidatos) {
            // Título do cargo
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(6);
            $pdf->MultiCell(0, 4, strtoupper($cargo), 0, 'L');
            $pdf->Ln(4);

            // Cabeçalho das colunas
            if ($count == 0) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell($w[0], 7, 'CPF', 0, 0, 'L', true);
                $pdf->Cell($w[1], 7, 'Nome do Candidato', 0, 0, 'L', true);
                $pdf->Cell($w[2], 7, 'Resultado', 0, 0, 'L', true);
                $pdf->Cell($w[3], 7, 'Modalidade', 0, 0, 'L', true);
            }
            $pdf->Ln(6);

            // Dados dos candidatos
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetFillColor(245, 245, 245);
            $fill = false;
            $count++;
            foreach ($candidatos as $c) {
                if ($c['modalidade'] == 'Ampla Concorrência') {
                    $pdf->Cell($w[0], 6, $c['cpf_candidato'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[1], 6, $c['nome_candidato'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[2], 6, $c['resultado'], 0, 0, 'L', $fill);
                    $pdf->Cell($w[3], 6, $c['modalidade'], 0, 0, 'L', $fill);
                    $pdf->Ln(6);
                    $fill = !$fill;
                }
            }

            // Linha separadora entre cargos
            $pdf->Ln(6);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - PDF_MARGIN_RIGHT, $pdf->GetY());
            $pdf->Ln(4);
        }
        // Parâmetro	Ação
        // 'I'	Inline — abre o PDF no navegador (nova aba normalmente)
        // 'D'	Download direto — o navegador baixa o arquivo
        // 'F'	Salva o arquivo em um caminho local do servidor
        // 'S'	Retorna o conteúdo como string (útil para enviar por e-mail, etc.)
        $pdf->Output('resultado_preliminar_deferidas_ampla.pdf', 'D');
        exit;
    }
}
