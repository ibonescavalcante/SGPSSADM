<?php

namespace App\models;

use App\core\Database;
use PDO;

class Avaliacao
{
    public static function criar($dados)
    {
        $sql = "INSERT INTO pss.avaliacao (
            inscricao_id,
            pss_etapa_id,
            nota,
            observacoes,
            avaliador_nome,
            dt_avaliacao,
            motivo_reprovacao
        ) VALUES (
            :inscricao_id,
            :pss_etapa_id,
            :nota,
            :observacoes,
            :avaliador_nome,
            :dt_avaliacao,
            :motivo_reprovacao
        )";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarPorInscricao($inscricao_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT a.*, e.nome as etapa_nome, e.peso as etapa_peso
            FROM pss.avaliacao a
            JOIN pss.pss_etapa e ON a.pss_etapa_id = e.id
            WHERE a.inscricao_id = :inscricao_id
            ORDER BY a.dt_avaliacao ASC
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorEtapa($etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT a.*, i.protocolo, c.nome as candidato_nome, c.cpf as candidato_cpf
            FROM pss.avaliacao a
            JOIN pss.inscricao i ON a.inscricao_id = i.id
            JOIN pss.candidato c ON i.candidato_id = c.id
            WHERE a.pss_etapa_id = :etapa_id
            ORDER BY a.nota DESC, a.dt_avaliacao ASC
        ");
        $stmt->execute(['etapa_id' => $etapa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT a.*, e.nome as etapa_nome, i.protocolo, c.nome as candidato_nome
            FROM pss.avaliacao a
            JOIN pss.pss_etapa e ON a.pss_etapa_id = e.id
            JOIN pss.inscricao i ON a.inscricao_id = i.id
            JOIN pss.candidato c ON i.candidato_id = c.id
            WHERE a.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.avaliacao SET 
            nota = :nota,
            observacoes = :observacoes,
            avaliador_nome = :avaliador_nome,
            dt_avaliacao = :dt_avaliacao,
            motivo_reprovacao = :motivo_reprovacao
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.avaliacao WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function calcularNotaFinal($inscricao_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                SUM(a.nota * e.peso) / SUM(e.peso) as nota_final,
                COUNT(*) as total_avaliacoes
            FROM pss.avaliacao a
            JOIN pss.pss_etapa e ON a.pss_etapa_id = e.id
            WHERE a.inscricao_id = :inscricao_id
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarClassificacao($pss_id, $cargo_id = null)
    {
        $sql = "
            SELECT 
                i.id as inscricao_id,
                i.protocolo,
                c.nome as candidato_nome,
                c.cpf as candidato_cpf,
                car.nome as cargo_nome,
                SUM(a.nota * e.peso) / SUM(e.peso) as nota_final,
                COUNT(a.id) as total_avaliacoes
            FROM pss.inscricao i
            JOIN pss.candidato c ON i.candidato_id = c.id
            JOIN pss.pss_cargo car ON i.pss_cargo_id = car.id
            LEFT JOIN pss.avaliacao a ON i.id = a.inscricao_id
            LEFT JOIN pss.pss_etapa e ON a.pss_etapa_id = e.id
            WHERE i.pss_id = :pss_id
        ";

        $params = ['pss_id' => $pss_id];

        if ($cargo_id) {
            $sql .= " AND i.pss_cargo_id = :cargo_id";
            $params['cargo_id'] = $cargo_id;
        }

        $sql .= "
            GROUP BY i.id, i.protocolo, c.nome, c.cpf, car.nome
            ORDER BY nota_final DESC, i.dt_inscricao ASC
        ";

        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function verificarAvaliacaoExistente($inscricao_id, $etapa_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id FROM pss.avaliacao 
            WHERE inscricao_id = :inscricao_id AND pss_etapa_id = :etapa_id
        ");
        $stmt->execute(['inscricao_id' => $inscricao_id, 'etapa_id' => $etapa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

