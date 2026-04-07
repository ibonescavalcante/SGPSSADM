<?php

namespace App\models;

use App\core\Database;
use PDO;

class Recurso
{
    public static function criar($dados)
    {
        $sql = "INSERT INTO pss.recurso (inscricao_id, etapa_id, conteudo, status, dt_abertura, dt_decisao, motivo_decisao, avaliador_id, motivo_abertura, resposta_candidato) 
                VALUES (:inscricao_id, :etapa_id, :conteudo, 'aberto', :dt_abertura, :dt_decisao, :motivo_decisao, :avaliador_id, :motivo_abertura, :resposta_candidato)";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarPorInscricao($inscricao_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            WHERE r.inscricao_id = :inscricao_id
            ORDER BY r.dt_abertura DESC
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, e.rec_ini, e.rec_fim, e.permite_recurso,
                   i.protocolo, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorCandidato($candidato_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, e.rec_ini, e.rec_fim, e.permite_recurso,
                   i.protocolo, p.titulo as pss_titulo, c.nome as cargo_nome
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            WHERE i.candidato_id = :candidato_id
            ORDER BY r.dt_abertura DESC
        ");
        $stmt->execute(['candidato_id' => $candidato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function responderRecurso($recurso_id, $resposta)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE pss.recurso 
            SET resposta_candidato = :resposta, 
                conteudo = :resposta,
                status = 'em_analise',
                dt_resposta_candidato = NOW()
            WHERE id = :id AND status = 'aberto'
        ");
        return $stmt->execute([
            'id' => $recurso_id,
            'resposta' => $resposta
        ]);
    }

    public static function finalizarRecurso($recurso_id, $resposta_avaliador, $decisao, $avaliador_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE pss.recurso 
            SET motivo_decisao = :resposta_avaliador,
                status = :decisao,
                dt_decisao = NOW(),
                avaliador_id = :avaliador_id
            WHERE id = :id AND status = 'em_analise'
        ");
        return $stmt->execute([
            'id' => $recurso_id,
            'resposta_avaliador' => $resposta_avaliador,
            'decisao' => $decisao, // 'deferido' ou 'indeferido'
            'avaliador_id' => $avaliador_id
        ]);
    }

    public static function verificarRecursoExistente($inscricao_id, $etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id FROM pss.recurso 
            WHERE inscricao_id = :inscricao_id AND etapa_id = :etapa_id
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id, 'etapa_id' => $etapa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function verificarPrazoRecurso($etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT permite_recurso, rec_ini, rec_fim 
            FROM pss.pss_etapa 
            WHERE id = :etapa_id
        ");
        $stmt->execute(['etapa_id' => $etapa_id]);
        $etapa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$etapa || !$etapa['permite_recurso']) {
            return false;
        }
        
        $agora = new \DateTime();
        $inicio = new \DateTime($etapa['rec_ini']);
        $fim = new \DateTime($etapa['rec_fim']);
        
        return $agora >= $inicio && $agora <= $fim;
    }

    public static function podeResponderRecurso($recurso_id)
    {
        $recurso = self::buscarPorId($recurso_id);
        
        if (!$recurso || $recurso['status'] !== 'aberto') {
            return false;
        }
        
        return self::verificarPrazoRecurso($recurso['etapa_id']);
    }

    public static function gerarProtocolo()
    {
        return 'REC' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function buscarPorProtocolo($protocolo)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, 
                   i.protocolo as protocolo_inscricao, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE i.protocolo = :protocolo
        ");
        $stmt->execute(['protocolo' => $protocolo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorStatus($status)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, 
                   i.protocolo, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE r.status = :status
            ORDER BY r.dt_abertura DESC
        ");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarTodos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, 
                   i.protocolo, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            ORDER BY r.dt_abertura DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorStatus($status)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM pss.recurso WHERE status = :status");
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public static function buscarPorEtapa($etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, e.nome as etapa_nome, 
                   i.protocolo, p.titulo as pss_titulo, c.nome as cargo_nome,
                   cand.nome as candidato_nome, cand.cpf as candidato_cpf
            FROM pss.recurso r
            JOIN pss.pss_etapa e ON r.etapa_id = e.id
            JOIN pss.inscricao i ON r.inscricao_id = i.id
            JOIN pss.pss p ON i.pss_id = p.id
            JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
            JOIN pss.candidato cand ON i.candidato_id = cand.id
            WHERE r.etapa_id = :etapa_id
            ORDER BY r.dt_abertura DESC
        ");
        $stmt->execute(['etapa_id' => $etapa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para o avaliador abrir um recurso (quando reprovar um candidato)
    public static function abrirRecurso($inscricao_id, $etapa_id, $motivo_indeferimento, $avaliador_id)
    {
        // Verificar se já existe recurso para esta inscrição/etapa
        if (self::verificarRecursoExistente($inscricao_id, $etapa_id)) {
            return false; // Já existe recurso
        }

        $dados = [
            'inscricao_id' => $inscricao_id,
            'etapa_id' => $etapa_id,
            'conteudo' => null, // Será preenchido quando o candidato responder
            'status' => 'aberto',
            'dt_decisao' => null,
            'motivo_decisao' => null,
            'avaliador_id' => $avaliador_id,
            'motivo_abertura' => $motivo_indeferimento,
            'resposta_candidato' => null
        ];

        return self::criar($dados);
    }

    public static function buscarRecursoPorInscricaoEEtapa($inscricao_id, $etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM pss.recurso 
            WHERE inscricao_id = :inscricao_id AND etapa_id = :etapa_id
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id, 'etapa_id' => $etapa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}