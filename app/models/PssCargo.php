<?php

namespace App\models;

use App\core\Database;
use PDO;

class PssCargo
{
    public static function buscarPorPss($pss_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss_cargo WHERE pss_id = :pss_id ORDER BY nome ASC");
        $stmt->execute(['pss_id' => $pss_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca cargos por PSS com contagem de inscrições em uma única consulta otimizada
     * Esta função resolve o problema de performance ao evitar múltiplas consultas
     */
    public static function buscarPorPssComInscricoes($pss_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                c.*,
                COALESCE(i.total_inscricoes, 0) as total_inscricoes
            FROM pss.pss_cargo c
            LEFT JOIN (
                SELECT 
                    pss_cargo_id, 
                    COUNT(*) as total_inscricoes 
                FROM pss.inscricao 
                WHERE status != 'cancelada'
                GROUP BY pss_cargo_id
            ) i ON c.id = i.pss_cargo_id
            WHERE c.pss_id = :pss_id 
            ORDER BY c.nome ASC
        ");
        $stmt->execute(['pss_id' => $pss_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM pss.pss_cargo WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $sql = "UPDATE pss.pss_cargo SET 
            nome = :nome,
            secretaria = :secretaria,
            vagas_total = :vagas_total,
            vagas_pcd = :vagas_pcd,
            vagas_ppp = :vagas_ppp,
            cadastro_reserva = :cadastro_reserva,
            requisitos_texto = :requisitos_texto,
            zona = :zona,
            microrregiao = :microrregiao,
            documentos_especificos = :documentos_especificos
        WHERE id = :id";

        $dados['id'] = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function excluir($id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pss.pss_cargo WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function buscarComVagas()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT c.*, p.titulo as pss_titulo, p.status_global
            FROM pss.pss_cargo c
            JOIN pss.pss p ON c.pss_id = p.id
            WHERE c.vagas_total > 0
            AND p.status_global = 'em_andamento'
            ORDER BY p.inscricao_fim ASC, c.nome ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarInscricoes($cargo_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) as total 
            FROM pss.inscricao 
            WHERE pss_cargo_id = :cargo_id
            AND status != 'cancelada'
        ");
        $stmt->execute(['cargo_id' => $cargo_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public static function buscarPorZona($zona)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT c.*, p.titulo as pss_titulo, p.status_global
            FROM pss.pss_cargo c
            JOIN pss.pss p ON c.pss_id = p.id
            WHERE c.zona = :zona
            AND c.vagas_total > 0
            AND p.status_global = 'em_andamento'
            ORDER BY p.inscricao_fim ASC, c.nome ASC
        ");
        $stmt->execute(['zona' => $zona]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorPssEZona($pss_id, $zona = null, $microrregiao = null, $pcd = null)
    {
        $sql = "SELECT * FROM pss.pss_cargo WHERE pss_id = :pss_id";
        $params = ['pss_id' => $pss_id];
        
        if ($zona && $zona !== 'todas') {
            $sql .= " AND zona = :zona";
            $params['zona'] = $zona;
        }
        
        if ($microrregiao && $microrregiao !== 'todas') {
            $sql .= " AND microrregiao = :microrregiao";
            $params['microrregiao'] = $microrregiao;
        }
        
        if ($pcd === 'sim') {
            $sql .= " AND vagas_pcd > 0";
        } elseif ($pcd === 'nao') {
            $sql .= " AND vagas_pcd = 0";
        }
        
        $sql .= " ORDER BY zona ASC, microrregiao ASC, nome ASC";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarZonasPorPss($pss_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT DISTINCT zona, COUNT(*) as total_cargos, SUM(vagas_total) as total_vagas
            FROM pss.pss_cargo 
            WHERE pss_id = :pss_id 
            GROUP BY zona 
            ORDER BY zona ASC
        ");
        $stmt->execute(['pss_id' => $pss_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarMicrorregioesPorPss($pss_id, $zona = null)
    {
        $sql = "
            SELECT DISTINCT microrregiao, COUNT(*) as total_cargos, SUM(vagas_total) as total_vagas
            FROM pss.pss_cargo 
            WHERE pss_id = :pss_id AND microrregiao IS NOT NULL
        ";
        $params = ['pss_id' => $pss_id];
        
        if ($zona && $zona !== 'todas') {
            $sql .= " AND zona = :zona";
            $params['zona'] = $zona;
        }
        
        $sql .= " GROUP BY microrregiao ORDER BY microrregiao ASC";
        
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function criar($dados)
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO pss.pss_cargo (
            pss_id, nome, zona, microrregiao, vagas_total, vagas_pcd, vagas_ppp,
            salario, requisitos, atribuicoes, documentos_especificos, cadastro_reserva
        ) VALUES (
            :pss_id, :nome, :zona, :microrregiao, :vagas_total, :vagas_pcd, :vagas_ppp,
            :salario, :requisitos, :atribuicoes, :documentos_especificos::jsonb, :cadastro_reserva
        )";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($dados);
    }

    public static function buscarDocumentosExigidos($pss_cargo_id)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT pcd.tipo_documento_codigo, td.nome as tipo_documento_nome, pcd.obrigatorio, td.multiplos_arquivos
            FROM pss.pss_cargo_documentos pcd
            JOIN pss.tipos_documento td ON pcd.tipo_documento_codigo = td.codigo
            WHERE pcd.pss_cargo_id = :pss_cargo_id
        ");
        $stmt->execute(['pss_cargo_id' => $pss_cargo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
