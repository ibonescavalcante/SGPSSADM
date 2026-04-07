<?php

namespace App\models;

use App\core\Database;
use PDO;

class Inscricao
{
    public static function criar($dados)
    {
        $sql = "INSERT INTO pss.inscricao (
            pss_id,
            pss_cargo_id,
            candidato_id,
            status,
            protocolo,
            dt_inscricao,
            extra_json
        ) VALUES (
            :pss_id,
            :pss_cargo_id,
            :candidato_id,
            :status,
            :protocolo,
            :dt_inscricao,
            :extra_json
        )";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function criarComDocumentos($dados)
    {
        $db = Database::getInstance();
        
        error_log("DEBUG: Iniciando criarComDocumentos para pss_id={$dados['pss_id']}, candidato_id={$dados['candidato_id']}");
        
        try {
            $db->beginTransaction();
            error_log("DEBUG: Transação iniciada");
            
            // PRIMEIRO: Verificar se o PSS permite múltiplas inscrições
            $stmt_pss = $db->prepare("
                SELECT permite_multiplas_inscricoes FROM pss.pss WHERE id = :pss_id
            ");
            $stmt_pss->execute(['pss_id' => $dados['pss_id']]);
            $pss_info = $stmt_pss->fetch(PDO::FETCH_ASSOC);
            
            if (!$pss_info) {
                error_log("DEBUG: PSS não encontrado");
                throw new \Exception("PSS não encontrado");
            }
            
            $permite_multiplas = $pss_info['permite_multiplas_inscricoes'];
            error_log("DEBUG: PSS permite múltiplas inscrições: " . ($permite_multiplas ? 'SIM' : 'NÃO'));
            
            // SEGUNDO: Verificar inscrições existentes
            $stmt_inscricoes = $db->prepare("
                SELECT id, status, protocolo FROM pss.inscricao 
                WHERE pss_id = :pss_id AND candidato_id = :candidato_id 
                ORDER BY dt_inscricao DESC
            ");
            $stmt_inscricoes->execute([
                'pss_id' => $dados['pss_id'],
                'candidato_id' => $dados['candidato_id']
            ]);
            $inscricoes_existentes = $stmt_inscricoes->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("DEBUG: Encontradas " . count($inscricoes_existentes) . " inscrições existentes");
            
            $tem_inscricao_ativa = false;
            $tem_inscricao_cancelada = false;
            
            foreach ($inscricoes_existentes as $insc) {
                error_log("DEBUG: Inscrição ID={$insc['id']}, Status={$insc['status']}");
                
                if (in_array($insc['status'], ['cancelado', 'cancelada'])) {
                    $tem_inscricao_cancelada = true;
                } else {
                    $tem_inscricao_ativa = true;
                }
            }
            
            error_log("DEBUG: Tem inscrição ativa: " . ($tem_inscricao_ativa ? 'SIM' : 'NÃO'));
            error_log("DEBUG: Tem inscrição cancelada: " . ($tem_inscricao_cancelada ? 'SIM' : 'NÃO'));
            
            // TERCEIRO: Aplicar regras de negócio
            if ($permite_multiplas) {
                // Se permite múltiplas inscrições
                if ($tem_inscricao_ativa) {
                    error_log("DEBUG: Já existe inscrição ativa e PSS permite múltiplas - não permitir nova inscrição");
                    throw new \Exception("Você já possui uma inscrição ativa para este PSS");
                }
                
                if ($tem_inscricao_cancelada) {
                    error_log("DEBUG: Existe inscrição cancelada e PSS permite múltiplas - permitir nova inscrição");
                    // Pode criar nova inscrição
                } else {
                    error_log("DEBUG: Não existe inscrição cancelada - permitir primeira inscrição");
                    // Pode criar primeira inscrição
                }
            } else {
                // Se NÃO permite múltiplas inscrições
                if ($tem_inscricao_ativa) {
                    error_log("DEBUG: Já existe inscrição ativa e PSS NÃO permite múltiplas - não permitir");
                    throw new \Exception("Você já possui uma inscrição para este PSS");
                }
                
                if ($tem_inscricao_cancelada) {
                    error_log("DEBUG: Existe inscrição cancelada e PSS NÃO permite múltiplas - permitir nova inscrição");
                    // Pode criar nova inscrição
                } else {
                    error_log("DEBUG: Não existe inscrição - permitir primeira inscrição");
                    // Pode criar primeira inscrição
                }
            }
            
            // QUARTO: Criar nova inscrição
            $protocolo = 'PSS' . date('Y') . time() . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            error_log("DEBUG: Criando nova inscrição com protocolo: $protocolo");
            
            // Preparar dados extras
            $extra_json = [
                'documentos' => $dados['documentos'] ?? [], // Agora pode ser um array de arrays para múltiplos arquivos
                'concorrer_pcd' => $dados['concorrer_pcd'] ?? 'nao',
                'laudo_pcd' => $dados['laudo_pcd'] ?? null,
                'data_inscricao' => date('Y-m-d H:i:s')
            ];

            // Converter os caminhos dos documentos para URLs relativas para armazenamento no banco de dados
            $documentos_para_db = [];
            foreach ($extra_json['documentos'] as $doc_ref => $paths) {
                if (is_array($paths)) {
                    $documentos_para_db[$doc_ref] = array_map(function($path) {
                        return str_replace($_SERVER['DOCUMENT_ROOT'] . '/../', '/', $path);
                    }, $paths);
                } else {
                    $documentos_para_db[$doc_ref] = str_replace($_SERVER['DOCUMENT_ROOT'] . '/../', '/', $paths);
                }
            }
            $extra_json['documentos'] = $documentos_para_db;
            
            // Inserir nova inscrição
            $sql = "INSERT INTO pss.inscricao (
                pss_id,
                pss_cargo_id,
                candidato_id,
                status,
                protocolo,
                dt_inscricao,
                extra_json,
                finalizada
            ) VALUES (
                :pss_id,
                :pss_cargo_id,
                :candidato_id,
                :status,
                :protocolo,
                NOW(),
                :extra_json,
                :finalizada
            )";
            
            $stmt = $db->prepare($sql);
            $resultado = $stmt->execute([
                'pss_id' => $dados['pss_id'],
                'pss_cargo_id' => $dados['cargo_id'], // Mapeando cargo_id para pss_cargo_id
                'candidato_id' => $dados['candidato_id'],
                'status' => $dados['status'] ?? 'apta',
                'protocolo' => $protocolo,
                'extra_json' => json_encode($extra_json),
                'finalizada' => true
            ]);
            
            if ($resultado) {
                // Verificar se a inserção realmente aconteceu
                $novo_id = $db->lastInsertId();
                error_log("DEBUG: Nova inscrição inserida com ID: $novo_id");
                
                $db->commit();
                error_log("DEBUG: Transação commitada com sucesso, protocolo: $protocolo");
                return $protocolo;
            } else {
                $db->rollBack();
                error_log("DEBUG: ERRO ao inserir nova inscrição, rollback executado");
                return false;
            }
            
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("DEBUG: EXCEÇÃO capturada: " . $e->getMessage());
            error_log("DEBUG: Rollback executado devido à exceção");
            
            throw $e; // Re-throw para permitir tratamento no controller
        }
    }

    public static function buscarPorCandidato($candidato_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.*, p.titulo as pss_titulo, c.nome as cargo_nome, p.inscricao_fim
            FROM pss.inscricao i
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            WHERE i.candidato_id = :candidato_id
            ORDER BY i.dt_inscricao DESC
        ");
        $stmt->execute(['candidato_id' => $candidato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.*, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf,
                   p.inscricao_fim
            FROM pss.inscricao i
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE i.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorProtocolo($protocolo)
    {
        $db = Database::getInstance();
        
        error_log("DEBUG: Buscando dados para protocolo: $protocolo");
        
        // Query simplificada usando apenas campos básicos que sabemos que existem
        $stmt = $db->prepare("
            SELECT i.*, 
                   p.titulo as pss_titulo, 
                   p.inscricao_ini,
                   p.inscricao_fim,
                   c.nome as cargo_nome,
                   c.zona,
                   c.vagas_total,
                   cand.nome as candidato_nome,
                   cand.cpf as candidato_cpf,
                   cand.email as candidato_email
            FROM pss.inscricao i
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE i.protocolo = :protocolo
        ");
        $stmt->execute(['protocolo' => $protocolo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("DEBUG: Resultado da busca por protocolo: " . ($resultado ? "encontrado" : "não encontrado"));
        
        return $resultado;
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.inscricao SET 
            status = :status,
            extra_json = :extra_json
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function atualizarStatus($id, $status)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pss.inscricao SET status = :status WHERE id = :id");
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public static function buscarPorCargo($cargo_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.*, cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.inscricao i
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE i.pss_cargo_id = :cargo_id
            ORDER BY i.dt_inscricao ASC
        ");
        $stmt->execute(['cargo_id' => $cargo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function gerarProtocolo()
    {
        // Gerar protocolo único com timestamp para evitar duplicatas
        $timestamp = time();
        $random = str_pad((string)mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return 'PSS' . date('Y') . $timestamp . $random;
    }

    public static function verificarInscricaoExistente($pss_id, $candidato_id)
    {
        $db = Database::getInstance();
        
        error_log("DEBUG: Verificando inscrição existente para pss_id=$pss_id, candidato_id=$candidato_id");
        
        // Buscar inscrição que NÃO esteja cancelada (nem cancelado nem cancelada)
        $stmt = $db->prepare("
            SELECT id, protocolo, status FROM pss.inscricao 
            WHERE pss_id = :pss_id AND candidato_id = :candidato_id 
            AND status NOT IN ('cancelado', 'cancelada')
        ");
        
        $stmt->execute(['pss_id' => $pss_id, 'candidato_id' => $candidato_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("DEBUG: Resultado da verificação (não canceladas): " . json_encode($resultado));
        
        return $resultado;
    }

    public static function buscarInscricaoExistenteQualquerStatus($pss_id, $candidato_id)
    {
        $db = Database::getInstance();
        
        error_log("DEBUG: Buscando inscrição existente (qualquer status) para pss_id=$pss_id, candidato_id=$candidato_id");
        
        $stmt = $db->prepare("
            SELECT id, protocolo, status FROM pss.inscricao 
            WHERE pss_id = :pss_id AND candidato_id = :candidato_id 
            ORDER BY dt_inscricao DESC 
            LIMIT 1
        ");
        
        $stmt->execute(['pss_id' => $pss_id, 'candidato_id' => $candidato_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("DEBUG: Resultado da busca (qualquer status): " . json_encode($resultado));
        
        return $resultado;
    }

    public static function buscarUltimaPorCandidato($candidato_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM pss.inscricao 
            WHERE candidato_id = :candidato_id 
            ORDER BY dt_inscricao DESC 
            LIMIT 1
        ");
        $stmt->execute(['candidato_id' => $candidato_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function contarTodas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM pss.inscricao");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function buscarRecentes($limite = 5)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT i.*, c.nome as candidato_nome, p.titulo as pss_titulo 
            FROM pss.inscricao i 
            JOIN pss.candidato c ON i.candidato_id = c.id 
            JOIN pss.pss p ON i.pss_id = p.id 
            ORDER BY i.dt_inscricao DESC 
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorMes()
    {
        $sql = "SELECT TO_CHAR(dt_inscricao, 'YYYY-MM') as mes, COUNT(*) as total 
                FROM pss.inscricao 
                GROUP BY mes 
                ORDER BY mes";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelar($id)
    {
        $db = Database::getInstance();
        
        try {
            // Verificar se a inscrição existe e não está já cancelada
            $stmt = $db->prepare("
                SELECT id, status FROM pss.inscricao 
                WHERE id = :id AND status NOT IN ('cancelado', 'cancelada')
            ");
            $stmt->execute(['id' => $id]);
            $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$inscricao) {
                return false; // Inscrição não encontrada ou já cancelada
            }
            
            // Atualizar status para cancelado
            $stmt = $db->prepare("
                UPDATE pss.inscricao 
                SET status = 'cancelado', 
                    extra_json = COALESCE(extra_json, '{}')::jsonb || '{\"data_cancelamento\": \"" . date('Y-m-d H:i:s') . "\"}'::jsonb
                WHERE id = :id
            ");
            
            return $stmt->execute(['id' => $id]);
            
        } catch (\Exception $e) {
            error_log("Erro ao cancelar inscrição: " . $e->getMessage());
            return false;
        }
    }
}

