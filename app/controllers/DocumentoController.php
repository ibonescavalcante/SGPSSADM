<?php

namespace App\controllers;

use App\models\Documento;
use App\models\Inscricao;
use App\models\Candidato;

class DocumentoController extends Controller
{
    private $tipos_permitidos = [
        'application/pdf',
        'image/jpeg',
        'image/jpg', 
        'image/png'
    ];

    private $tamanho_maximo = 5242880; // 5MB

    public function upload($inscricao_id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        $inscricao = Inscricao::buscarPorId($inscricao_id);

        if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
            header('Location: /painel?erro=Inscrição não encontrada');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'] ?? '';
            
            if (empty($tipo)) {
                $erro = 'Tipo de documento é obrigatório';
            } elseif (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                $erro = 'Erro no upload do arquivo';
            } else {
                $arquivo = $_FILES['arquivo'];
                
                // Validar tipo de arquivo
                if (!in_array($arquivo['type'], $this->tipos_permitidos)) {
                    $erro = 'Tipo de arquivo não permitido. Use PDF, JPG ou PNG';
                } elseif ($arquivo['size'] > $this->tamanho_maximo) {
                    $erro = 'Arquivo muito grande. Máximo 5MB';
                } else {
                    // Verificar se já existe documento deste tipo
                    $documento_existente = Documento::buscarPorTipo($inscricao_id, $tipo);
                    if ($documento_existente) {
                        $erro = 'Já existe um documento deste tipo. Exclua o anterior antes de enviar um novo';
                    } else {
                        // Criar diretório se não existir
                        $diretorio = "uploads/documentos/{$inscricao_id}/";
                        if (!is_dir($diretorio)) {
                            mkdir($diretorio, 0755, true);
                        }

                        // Gerar nome único para o arquivo
                        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
                        $nome_arquivo = $tipo . '_' . time() . '.' . $extensao;
                        $caminho_completo = $diretorio . $nome_arquivo;

                        if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
                            $dados = [
                                'inscricao_id' => $inscricao_id,
                                'tipo' => $tipo,
                                'nome_arquivo' => $arquivo['name'],
                                'caminho_arquivo' => $caminho_completo,
                                'tamanho_bytes' => $arquivo['size'],
                                'mime_type' => $arquivo['type'],
                                'status_validacao' => 'pendente',
                                'observacoes' => null
                            ];

                            if (Documento::criar($dados)) {
                                header("Location: /painel/documentos/{$inscricao_id}?success=Documento enviado com sucesso");
                                exit;
                            } else {
                                $erro = 'Erro ao salvar documento no banco de dados';
                                unlink($caminho_completo); // Remove arquivo se falhou
                            }
                        } else {
                            $erro = 'Erro ao mover arquivo para diretório de destino';
                        }
                    }
                }
            }
        }

        $documentos = Documento::buscarPorInscricao($inscricao_id);

        $this->view('candidato/upload_documento', [
            'inscricao' => $inscricao,
            'documentos' => $documentos,
            'erro' => $erro ?? null,
            'tipos_documento' => $this->getTiposDocumento()
        ]);
    }

    public function excluir($id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        $documento = Documento::buscarPorId($id);

        if (!$documento) {
            header('Location: /painel?erro=Documento não encontrado');
            exit;
        }

        $inscricao = Inscricao::buscarPorId($documento['inscricao_id']);
        if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
            header('Location: /painel?erro=Acesso negado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Excluir arquivo físico
            if (file_exists($documento['caminho_arquivo'])) {
                unlink($documento['caminho_arquivo']);
            }

            // Excluir registro do banco
            if (Documento::excluir($id)) {
                header("Location: /painel/documentos/{$documento['inscricao_id']}?success=Documento excluído com sucesso");
                exit;
            } else {
                header("Location: /painel/documentos/{$documento['inscricao_id']}?erro=Erro ao excluir documento");
                exit;
            }
        }

        header('Location: /painel');
        exit;
    }

    public function download($id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        $candidato_id = $_SESSION['usuario']['id'];
        $documento = Documento::buscarPorId($id);

        if (!$documento) {
            header('Location: /painel?erro=Documento não encontrado');
            exit;
        }

        $inscricao = Inscricao::buscarPorId($documento['inscricao_id']);
        if (!$inscricao || $inscricao['candidato_id'] != $candidato_id) {
            header('Location: /painel?erro=Acesso negado');
            exit;
        }

        $caminho_base = __DIR__ . '/../../public/';
        $caminho_relativo = $documento["caminho_arquivo"];
        $caminho_completo = realpath($caminho_base . $caminho_relativo);

        if (!$caminho_completo || !file_exists($caminho_completo)) {
            header('Location: /painel?erro=Arquivo não encontrado no servidor');
            exit;
        }

        // Forçar download
        header('Content-Type: ' . $documento['mime_type']);
        header('Content-Disposition: attachment; filename="' . $documento['nome_arquivo'] . '"');
        header('Content-Length: ' . filesize($caminho_completo));
        
        readfile($caminho_completo);
        exit;
    }

    // Métodos administrativos
    public function validar($id)
    {
        $documento = Documento::buscarPorId($id);
        if (!$documento) {
            header('Location: /admin/documentos?erro=Documento não encontrado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';

            if (Documento::atualizarStatus($id, $status, $observacoes)) {
                header('Location: /admin/documentos?success=Documento validado');
                exit;
            } else {
                $erro = 'Erro ao validar documento';
            }
        }

        $this->view('admin/documentos/validar', [
            'documento' => $documento,
            'erro' => $erro ?? null
        ]);
    }

    public function listarPendentes()
    {
        $documentos = Documento::buscarPendentesValidacao();

        $this->view('admin/documentos/pendentes', [
            'documentos' => $documentos
        ]);
    }

    public function visualizar($id)
    {
        $documento = Documento::buscarPorId($id);
        if (!$documento) {
            header('Location: /admin/documentos?erro=Documento não encontrado');
            exit;
        }

        $caminho_base = __DIR__ . '/../../public/';
        $caminho_relativo = $documento["caminho_arquivo"];
        $caminho_completo = realpath($caminho_base . $caminho_relativo);

        if (!$caminho_completo || !file_exists($caminho_completo)) {
            header('Location: /admin/documentos?erro=Arquivo não encontrado no servidor');
            exit;
        }

        // Exibir documento no navegador
        header('Content-Type: ' . $documento['mime_type']);
        readfile($caminho_completo);
        exit;
    }

    private function getTiposDocumento()
    {
        return [
            'rg' => 'RG (Frente e Verso)',
            'cpf' => 'CPF',
            'comprovante_residencia' => 'Comprovante de Residência',
            'diploma' => 'Diploma/Certificado de Escolaridade',
            'curriculo' => 'Currículo',
            'foto' => 'Foto 3x4',
            'certidao_nascimento' => 'Certidão de Nascimento',
            'titulo_eleitor' => 'Título de Eleitor',
            'certificado_reservista' => 'Certificado de Reservista',
            'pcd_laudo' => 'Laudo Médico (PCD)',
            'ppp_declaracao' => 'Declaração (PPP)',
            'outros' => 'Outros Documentos'
        ];
    }

    public function validarObrigatorios($inscricao_id)
    {
        $tipos_obrigatorios = ['rg', 'cpf', 'comprovante_residencia', 'diploma', 'foto'];
        
        $validacao = Documento::validarTiposObrigatorios($inscricao_id, $tipos_obrigatorios);
        
        if ($validacao) {
            // Atualizar status da inscrição para "documentos_completos"
            Inscricao::atualizarStatus($inscricao_id, 'documentos_completos');
        }

        return $validacao;
    }
}


