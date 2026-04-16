<?php

namespace App\controllers;

use App\models\PssCargoDashboard;
use App\models\InscricaoDashboard;
use App\models\Usuario;

//use League\Plates\Engine;

// use App\models\Candidato;
// use App\models\Inscricao;
// use App\models\Pss;
use App\core\Controller;
class ApiController extends Controller
{




    public function get_cargos_by_pss_id($id)
    {
        $PssCargoDashboard = new PssCargoDashboard();

        header("Content-Type: application/json");
        try {
            $cargos = $PssCargoDashboard->busca_cargo_by_processo_id($id);
            echo json_encode($cargos);
        } catch (\Exception $e) {

            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
        // return $PssCargoDashboard->busca_cargo_by_processo_id(1);
    }
    public function get_inscricoes()
    {
        // var_dump($_POST);
        // die;
        header("Content-Type: application/json");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método não permitido
            echo json_encode(["erro" => "Método não permitido. Use POST."]);
            return;
        }
        $processo_id = $_POST['process_id'] ?? null;
        $cargo_id    = $_POST['cargo_id'] ?? null;
        $page    = $_POST['page'] ?? 1;
        $limite    = $_POST['limite'] ?? 10;
        $cpf    = $_POST['cpf'] ?? '';
        $nome = strtoupper($_POST['nome'] ?? '');
        $status    = strtolower($_POST['status']) ?? '';
        $vagaTipo    = $_POST['vagaTipo'] ?? '';


        if (empty($processo_id) || empty($cargo_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(["erro" => "Parâmetros 'processo_id' e 'cargo_id' são obrigatórios."]);
            return;
        }
        $InscricaoDashboard = new InscricaoDashboard();
        try {
            $cargos = $InscricaoDashboard->busca_inscricoes_by_processo_id_cargo_id($processo_id, $cargo_id, $cpf, $status, $limite, $page, $vagaTipo, $nome);

            echo json_encode($cargos);
        } catch (\Exception $e) {
            error_log("Erro ao buscar inscrições: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }
    public function get_recursos()
    {
        // var_dump($_POST);
        // die;
        header("Content-Type: application/json");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método não permitido
            echo json_encode(["erro" => "Método não permitido. Use POST."]);
            return;
        }
        $processo_id = $_POST['process_id'] ?? null;
        $page    = $_POST['page'] ?? 1;
        $limite    = $_POST['limite'] ?? 10;
        $status    = $_POST['status'] ?? '';


        // var_dump($_POST);
        // die;

        if (empty($processo_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(["erro" => "Parâmetros 'processo_id' são obrigatórios."]);
            return;
        }
        $InscricaoDashboard = new InscricaoDashboard();
        try {
            $cargos = $InscricaoDashboard->busca_recursos_by_processo_id_status_id($processo_id, $status, $limite, $page);
            echo json_encode($cargos);
        } catch (\Exception $e) {
            error_log("Erro ao buscar inscrições: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }

    public function get_processo_status($processo_id)
    {
        $InscricaoDashboard = new InscricaoDashboard();

        header("Content-Type: application/json");
        try {

            $result = $InscricaoDashboard->busca_inscricoes_process_id_status($processo_id);
            echo json_encode($result[0]);
        } catch (\Exception $e) {

            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }

    /**
     * Registra pontuação de título via modal (JSON).
     */
    public function setAvaliacaoTitulo(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.']);
            return;
        }
        $usuario_id = $_SESSION['user']['id'] ?? null;
        if (!$usuario_id) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado.']);
            return;
        }
        $inscricao_id = $_POST['inscricao_id'] ?? null;
        $tipo_documento = $_POST['tipo_documento'] ?? '';
        $pontos = $_POST['pontos'] ?? null;
        if ($inscricao_id === null || $inscricao_id === '' || $pontos === null || $pontos === '') {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos.']);
            return;
        }
        try {
            InscricaoDashboard::set_inscricao_pontuacao_titulo(
                (int) $usuario_id,
                (int) $inscricao_id,
                (string) $tipo_documento,
                'Avaliação de título (modal painel)',
                $pontos,
                true
            );
            echo json_encode(['sucesso' => true, 'mensagem' => 'Pontuação registrada com sucesso.']);
        } catch (\Throwable $e) {
            error_log('setAvaliacaoTitulo: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar a avaliação.']);
        }
    }

    //setar inscricao status deferido e indeferido
    public function set_inscricao_status($routeInscricaoId = null)
    {

        $usuario_id = (int) ($_SESSION['user']['id'] ?? 0);
        // $avaliador_id = $_POST['avaliador_id'] ?? 0;
        $inscricao_id = $_POST['inscricao_id'] ?? null;
        $status    = strtolower($_POST['status']) ?? null;
        $justificativa    = $_POST['justificativa'] ?? null;


        if ($status == 'indeferido' && $justificativa == "") {
            // echo ("justificativa vazia");
            $_SESSION['erro'] = "E preciso justificar o indeferimento.";
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
            return;
        }

        $InscricaoDashboard = new InscricaoDashboard();
        try {
            $InscricaoDashboard->set_inscricao_deferido_indeferido($usuario_id, $inscricao_id, $status, $justificativa);
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        } catch (\Exception $e) {

            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }
    public function set_recursos_status($routeRecursoId = null)
    {

        // var_dump($_POST);
        // die;

        $usuario_id = (int) ($_SESSION['user']['id'] ?? 0);
        $avaliador_id = $_POST['avaliador_id'] ?? null;
        $recurso_id = $_POST['recurso_id'] ?? null;
        $status    = strtolower($_POST['status']) ?? null;
        $justificativa    = $_POST['justificativa'] ?? null;
        // echo  $usuario_id;
        // die;



        if ($avaliador_id != null && $usuario_id != $avaliador_id) {
            $_SESSION['erro'] = "Usuário não tem permissão para alterar.";
            header("Location: /dashboard/recursos/detalhes/" . $recurso_id);
            return;
        }

        if ($status == 'indeferido' && $justificativa == "") {
            // echo ("justificativa vazia");
            $_SESSION['erro'] = "E preciso justificar o indeferimento.";
            header("Location: /dashboard/recursos/detalhes/" . $recurso_id);
            return;
        }

        $InscricaoDashboard = new InscricaoDashboard();
        try {
            $InscricaoDashboard->set_recurso_deferido_indeferido($usuario_id, $recurso_id, $status, $justificativa);
            header("Location: /dashboard/recursos/detalhes/" . $recurso_id);
        } catch (\Exception $e) {

            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }
    public function set_inscricao_pontuacao()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido. Use POST."]);
            return;
        }


        $usuario_id = (int) ($_SESSION['user']['id'] ?? 0);
        if ($usuario_id < 1) {
            http_response_code(401);
            echo json_encode(["erro" => "Não autenticado."]);
            return;
        }
        $inscricao_id = $_POST['inscricao_id'] ?? null;
        $avaliador_id = $_POST['avaliador_id'] ?? 0;
        $tipo_documento = $_POST['doc_tipo'] ?? null;
        $justificativa_pontuacao    = $_POST['justificativa_pontuacao'] ?? null;
        $pontos_titulo    = $_POST['pontos-titulo'] ?? null;

        // if ($avaliador_id != 0 && $usuario_id != $avaliador_id) {
        //     $_SESSION['erro'] = "Usuário não tem permissão para alterar.";
        //     header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        //     return;
        // }
        $InscricaoDashboard = new InscricaoDashboard();
        try {
            $InscricaoDashboard->set_inscricao_pontuacao_titulo($usuario_id, $inscricao_id, $tipo_documento, $justificativa_pontuacao, $pontos_titulo);
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        } catch (\Exception $e) {

            error_log("Erro ao buscar cidades: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }
    public function excluirPontuacao()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido. Use POST."]);
            return;
        }
        $usuario_id = $_SESSION['user']['id'] ?? null;
        $pontuacao_id = $_POST['pontuacao_id'] ?? null;
        $inscricao_id = $_POST['inscricao_id'] ?? null;
        $avaliador_id = $_POST['avaliador_id'] ?? null;

        // if ($avaliador_id != 0 && $usuario_id != $avaliador_id) {
        //     $_SESSION['erro'] = "Usuário não tem permissão para alterar.";
        //     header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        //     return;
        // }

        if (empty($pontuacao_id)) {
            http_response_code(400);
            echo json_encode(["erro" => "O ID da pontuação é obrigatório."]);
            return;
        }

        $inscricaoDashboard = new InscricaoDashboard();

        try {
            $resultado = $inscricaoDashboard->excluirPontuacaoPorId($pontuacao_id, $avaliador_id);
            if ($resultado) {
                header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
                exit;
            } else {
                http_response_code(500);
                echo json_encode(["erro" => "Não foi possível excluir a pontuação."]);
            }
        } catch (\Exception $e) {
            error_log("Erro ao excluir pontuação: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
        }
    }

    public function alterarSenha()
    {
        header("Content-Type: application/json");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Método não permitido
            echo json_encode(["sucesso" => false, "mensagem" => "Método não permitido. Use POST."]);
            return;
        }

        // $dados = json_decode(file_get_contents('php://input'), true);
        $senhaAtual = $_POST['senha_atual'] ?? null;
        $novaSenha = $_POST['nova_senha'] ?? null;
        $usuarioId = $_SESSION['user']['id'] ?? null;
        // $usuarioEmail = $_SESSION['user']['email'] ?? null;
        // echo ($usuarioId);
        // echo json_encode(["sucesso" => false, "mensagem" => "Método não permitido. Use POST."]);
        // // var_dump($_POST);
        // die;

        if (!$senhaAtual || !$novaSenha || !$usuarioId) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "Todos os campos são obrigatórios."]);
            return;
        }

        // Verificar se a senha atual está correta
        $resultado = Usuario::verificarSenha($usuarioId, $senhaAtual);

        if (!$resultado) {
            http_response_code(401);
            echo json_encode(["sucesso" => false, "mensagem" => "A senha atual está incorreta."]);
            return;
        }

        // Criptografar a nova senha
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        // var_dump($novaSenhaHash);
        // die;
        // Atualizar a senha no banco de dados
        if (Usuario::atualizarSenha($usuarioId, $novaSenhaHash)) {
            echo json_encode(["sucesso" => true, "mensagem" => "Senha alterada com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Ocorreu um erro ao alterar a senha. Tente novamente."]);
        }
    }

    public function alterarDocumento()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido. Use POST."]);
            return;
        }

        $inscricao_id = $_POST['inscricao_id'] ?? null;
        $documento_tipo = $_POST['documento_tipo'] ?? null;
        $novo_arquivo = $_FILES['novo_arquivo'] ?? null;

        if (empty($inscricao_id) || empty($documento_tipo) || empty($novo_arquivo)) {
            $_SESSION['erro'] = "Dados insuficientes para alterar o documento.";
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
            return;
        }

        if ($novo_arquivo['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['erro'] = "Erro no upload do arquivo.";
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
            return;
        }

        $inscricaoDashboard = new InscricaoDashboard();
        try {
            $inscricaoDashboard->alterarDocumento($documento_tipo, $novo_arquivo);
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        } catch (\Exception $e) {
            $_SESSION['erro'] = "Erro ao alterar o documento: " . $e->getMessage();
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        }
    }



    // public function relatorios()
    // {
    //     $this->view('dashboard/relatorios/page');
    // }
    // public function configuracoes()
    // {
    //     $this->view('dashboard/configuracoes/page');
    // }
    // public function processos()
    // {
    //     $this->view('dashboard/processos/page');
    // }
    // public function processos_novo()
    // {
    //     $this->view('dashboard/processos/novo/page');
    // }
    // public function detalhes($id)
    // {
    //     // echo ($id);
    //     // die;
    //     $this->view('dashboard/candidatos/detalhes');
    // }
}
