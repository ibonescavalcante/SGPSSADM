<?php

namespace App\models;

use App\core\Database;
use PDO;

class PssDashboard
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function buscar_processo_by_status($statuses)
    {
        // Garante que é array
        $statuses = (array)$statuses;

        // Cria placeholders
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));

        $sql = "
        SELECT 
            id,titulo                
        FROM pss.pss          
        WHERE status_global IN ($placeholders)
        GROUP BY id
        ORDER BY criado_em DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($statuses);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.pss SET 
            titulo = :titulo,
            secretaria = :secretaria,
            ano_exercicio = :ano_exercicio,
            descricao = :descricao,
            status_global = :status_global,
            inscricao_ini = :inscricao_ini,
            inscricao_fim = :inscricao_fim,
            metas_json = :metas_json,
            atualizado_em = NOW()
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.pss WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * ✅ CORREÇÃO: Método buscarAbertos melhorado para incluir PSS em breve e ativos
     */
    public static function buscarAbertos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                p.*,
                COUNT(pc.id) as total_cargos,
                COALESCE(SUM(pc.vagas_total), 0) as total_vagas,
                COALESCE(SUM(pc.vagas_pcd), 0) as total_pcd,
                COALESCE(SUM(pc.vagas_ppp), 0) as total_ppp,
                CASE 
                    WHEN p.inscricao_ini > NOW() THEN 'em_breve'
                    WHEN p.inscricao_ini <= NOW() AND p.inscricao_fim >= NOW() THEN 'aberto'
                    WHEN p.inscricao_fim < NOW() THEN 'encerrado'
                    ELSE 'indefinido'
                END as status_inscricao
            FROM pss.pss p
            LEFT JOIN pss.pss_cargo pc ON p.id = pc.pss_id
            WHERE p.status_global = 'em_andamento'
            AND (
                -- PSS com inscrições em breve (próximos 30 dias)
                (p.inscricao_ini > NOW() AND p.inscricao_ini <= NOW() + INTERVAL '30 days')
                OR 
                -- PSS com inscrições abertas
                (p.inscricao_ini <= NOW() AND p.inscricao_fim >= NOW())
            )
            GROUP BY p.id, p.titulo, p.secretaria, p.ano_exercicio, p.status_global, p.inscricao_ini, p.inscricao_fim, p.publicado_em, p.versao, p.metas_json, p.criado_em, p.atualizado_em, p.descricao
            HAVING COUNT(pc.id) > 0
            ORDER BY 
                CASE 
                    WHEN p.inscricao_ini <= NOW() AND p.inscricao_fim >= NOW() THEN 1
                    WHEN p.inscricao_ini > NOW() THEN 2
                    ELSE 3
                END,
                p.inscricao_ini ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorStatus(string $status): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM pss.pss WHERE status_global = :status');
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public static function buscarPorStatus(string $status, ?int $limite = null): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT 
                p.*,
                COUNT(pc.id) as total_vagas,
                COALESCE(SUM(pc.vagas_total), 0) as total_posicoes,
                COALESCE(SUM(pc.vagas_pcd), 0) as total_pcd,
                COALESCE(SUM(pc.vagas_ppp), 0) as total_ppp
            FROM pss.pss p
            LEFT JOIN pss.pss_cargo pc ON p.id = pc.pss_id
            WHERE p.status_global = :status
            GROUP BY p.id, p.titulo, p.secretaria, p.ano_exercicio, p.status_global, p.inscricao_ini, p.inscricao_fim, p.publicado_em, p.versao, p.metas_json, p.criado_em, p.atualizado_em, p.descricao
            ORDER BY p.criado_em DESC
        ";
        if ($limite !== null) {
            $sql .= ' LIMIT :limite';
        }
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        if ($limite !== null) {
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarAtivosComVagas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                p.*,
                COUNT(pc.id) as total_vagas,
                SUM(pc.vagas_total) as total_posicoes,
                SUM(pc.vagas_pcd) as total_pcd,
                SUM(pc.vagas_ppp) as total_ppp
            FROM pss.pss p
            LEFT JOIN pss.pss_cargo pc ON p.id = pc.pss_id
            WHERE p.status_global = 'em_andamento'
            AND (p.inscricao_ini IS NULL OR p.inscricao_ini <= CURRENT_DATE)
            AND (p.inscricao_fim IS NULL OR p.inscricao_fim >= CURRENT_DATE)
            GROUP BY p.id, p.titulo, p.secretaria, p.ano_exercicio, p.status_global, p.inscricao_ini, p.inscricao_fim, p.publicado_em, p.versao, p.metas_json, p.criado_em, p.atualizado_em, p.descricao
            HAVING COUNT(pc.id) > 0
            ORDER BY p.criado_em DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarAtivosComVagasPorZona($zona)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                p.*,
                COUNT(pc.id) as total_vagas,
                SUM(pc.vagas_total) as total_posicoes,
                SUM(pc.vagas_pcd) as total_pcd,
                SUM(pc.vagas_ppp) as total_ppp
            FROM pss.pss p
            LEFT JOIN pss.pss_cargo pc ON p.id = pc.pss_id
            WHERE p.status_global = 'em_andamento'
            AND (p.inscricao_ini IS NULL OR p.inscricao_ini <= CURRENT_DATE)
            AND (p.inscricao_fim IS NULL OR p.inscricao_fim >= CURRENT_DATE)
            AND pc.zona = :zona
            GROUP BY p.id, p.titulo, p.secretaria, p.ano_exercicio, p.status_global, p.inscricao_ini, p.inscricao_fim, p.publicado_em, p.versao, p.metas_json, p.criado_em, p.atualizado_em, p.descricao
            HAVING COUNT(pc.id) > 0
            ORDER BY p.criado_em DESC
        ");
        $stmt->execute(['zona' => $zona]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarTodos()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM pss.pss");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function buscarRecentes($limite = 5)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss ORDER BY criado_em DESC LIMIT :limite");
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
