<?php

namespace App\models;

use App\core\Database;
use PDO;

class InscricaoDashboard
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function busca_inscricoes_by_processo_id_cargo_id($processo_id, $cargo_id,  $cpf, $status, $limite, $page, $vagaTipo, $nome)
    {
        // echo ($status);
        // die;

        $cpfLike = "%{$cpf}%";
        $nomeLike = "%{$nome}%";
        $sql = "SELECT COUNT(*) OVER() AS total_registros,
                    i.id,
                    i.protocolo,
                    i.status,
                    cand.nome as candidato_nome,
                    cand.cpf as candidato_cpf,
                    p.titulo as pss_titulo,  
                    c.nome as cargo_nome,
                    p.inscricao_ini as inscricao_ini2,
                    i.criado_em as inscricao_ini
                FROM pss.inscricao i
                JOIN pss.pss p ON i.pss_id = p.id
                JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id    
                JOIN pss.candidato cand ON i.candidato_id = cand.id
                WHERE i.pss_id = :pss_id
                --AND i.pss_cargo_id = :pss_cargo_id
                AND c.nome = :pss_cargo_id
                -- AND i.status = :status_insc             
                and cand.cpf like :cpf
                and cand.nome like :nome
                and i.status_inscricao = 'Ativa'";

        // AND i.tipo_inscricao = :vagaTipo
        // ORDER BY cand.nome ASC
        // LIMIT :limit_page  OFFSET :offset_page;";

        if (!empty($vagaTipo) && $vagaTipo != 'all') {
            $sql .= " AND i.tipo_inscricao = :vagaTipo";
        }
        if (!empty($status) && $status != 'all') {
            $sql .= " AND i.status = :status_insc";
        }

        $sql .= " ORDER BY cand.nome ASC
          LIMIT :limit_page OFFSET :offset_page;";
        $stmt = $this->db->prepare($sql);
        $params = [
            'pss_id'       => $processo_id,
            'pss_cargo_id' => $cargo_id,
            // 'status_insc'  => $status,
            'cpf'          => "%{$cpf}%",
            'nome'          => "%{$nome}%",
            'offset_page'  => ($page * $limite),
            'limit_page'   => $limite,
        ];
        if (!empty($vagaTipo) && $vagaTipo != 'all') {
            $params['vagaTipo'] = $vagaTipo;
        }
        if (!empty($status) && $status != 'all') {
            $params['status_insc'] = $status;
        }

        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    //ajustando para que se os parametros forem passados e acrescentado na consulta
    public function busca_inscricoes_by_processo_id_cargo_id_ajustando($processo_id, $cargo_id, $cpf, $status, $limite, $page, $vagaTipo, $nome)
    {
        echo ($status);
        die;
        $sql = "SELECT COUNT(*) OVER() AS total_registros,
               i.id,
               i.protocolo,
               i.status,
               cand.nome AS candidato_nome,
               cand.cpf AS candidato_cpf,
               p.titulo AS pss_titulo,  
               c.nome AS cargo_nome,
               p.inscricao_ini AS inscricao_ini2,
               i.criado_em AS inscricao_ini
        FROM pss.inscricao i
        JOIN pss.pss p ON i.pss_id = p.id
        JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id    
        JOIN pss.candidato cand ON i.candidato_id = cand.id
        WHERE i.pss_id = :pss_id and i.status_inscricao = 'Ativa'";

        $params = [
            'pss_id'      => $processo_id,
            'offset_page' => ($page * $limite),
            'limit_page'  => $limite,
        ];

        // Filtros opcionais dinamicamente adicionados
        if (!empty($cargo_id) && $cargo_id != 'all') {
            $sql .= " AND c.nome = :pss_cargo_id";
            $params['pss_cargo_id'] = $cargo_id;
        }

        if (!empty($cpf)) {
            $sql .= " AND cand.cpf LIKE :cpf";
            $params['cpf'] = "%{$cpf}%";
        }

        if (!empty($nome)) {
            $sql .= " AND cand.nome LIKE :nome";
            $params['nome'] = "%{$nome}%";
        }

        if (!empty($status) && $status != 'all') {
            $sql .= " AND i.status = :status_insc";
            $params['status_insc'] = $status;
        }

        if (!empty($vagaTipo) && $vagaTipo != 'all') {
            $sql .= " AND i.tipo_inscricao = :vagaTipo";
            $params['vagaTipo'] = $vagaTipo;
        }

        $sql .= "
        ORDER BY cand.nome ASC
        LIMIT :limit_page OFFSET :offset_page;
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }



    public function busca_recursos_by_processo_id_status_id($processo_id, $status, $limite, $page)
    {
        // Monta a query base
        $sql = "SELECT COUNT(*) OVER() AS total_registros,
                   r.id AS recurso,
                   i.id AS inscricao,
                   i.protocolo AS inscricao_protocolo,
                   c.nome AS nome,
                   r.status AS status
            FROM pss.recurso r
            JOIN pss.inscricao i ON i.id = r.inscricao_id
            JOIN pss.candidato c ON c.id = i.candidato_id
            WHERE r.resposta_candidato is not null and i.pss_id = :pss_id";

        // Adiciona o filtro de status, se aplicável
        if (!empty($status) && $status !== 'todos') {
            $sql .= " AND r.status = :status";
        }

        // Adiciona ordenação e paginação
        $sql .= " ORDER BY c.nome ASC
              LIMIT :limit_page OFFSET :offset_page";

        // Prepara a query
        $stmt = $this->db->prepare($sql);

        // Define os parâmetros
        $params = [
            'pss_id'      => $processo_id,
            'limit_page'  => $limite,
            'offset_page' => ($page * $limite),
        ];

        // Só adiciona o parâmetro se o status for válido
        if (!empty($status) && $status !== 'todos') {
            $params['status'] = $status;
        }

        // Executa e retorna os resultados
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // public function busca_recursos_detalhes($processo_id, $status, $limite, $page)
    public function busca_recursos_detalhes($id_recurso)
    {
        $sql = "SELECT
                p.titulo AS processo_nome,
                pc.nome_cargo AS nome_do_cargo,
                pc.zona AS zona,
                c.nome AS nome,
                c.cpf AS cpf,
                i.protocolo AS inscricao,
                i.id AS inscricao_id,
                r.id ,
                r.status AS status,
                r.motivo_abertura AS descricao,
                r.resposta_candidato AS recurso,
                r.motivo_decisao AS justificativa,
                u.nome AS nome_avaliador
            FROM pss.recurso r
            JOIN pss.inscricao i ON i.id = r.inscricao_id 
            JOIN pss.candidato c ON c.id = i.candidato_id 
            JOIN pss.pss p ON p.id = i.pss_id 
            JOIN pss.pss_cargo pc ON pc.id = i.pss_cargo_id 
            LEFT JOIN pss.usuario u ON u.id = r.avaliador_id
            WHERE r.id =:id_recurso";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_recurso' => $id_recurso]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function busca_inscricoes_by_processo_id_cargo_id2($processo_id, $cargo_id, $limit, $page)
    {
        $sql = "SELECT  COUNT(*) OVER() AS total_registros, 
         i.id,
         i.protocolo,
         i.status,
                cand.nome as candidato_nome,
                cand.cpf as candidato_cpf,
                p.titulo as pss_titulo,  
                c.nome as cargo_nome,
                p.inscricao_ini as inscricao_ini2,
                i.criado_em as inscricao_ini
                FROM pss.inscricao i
                JOIN pss.pss p ON i.pss_id = p.id
                JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
                JOIN pss.candidato cand ON i.candidato_id = cand.id
                WHERE i.pss_id  = :pss_id and i.pss_cargo_id = :pss_cargo_id LIMIT :limit_page offset :offset_page";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pss_id' => $processo_id, 'pss_cargo_id' => $cargo_id, 'limit_page' => $limit, 'offset_page' => ($page * $limit)]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function busca_inscricoes_by_id($inscricao_id)
    {
        $sql = "SELECT          
                i.id,
                i.protocolo,
                i.status,
                i.extra_json ,
                i.motivo_cancelamento ,
                i.avaliador_id,
                i.atualizado_em ,
                cand.nome as candidato_nome,
              --  usuario.nome as usuario_nome,
                cand.cpf as candidato_cpf,
                cand.email as candidato_email,
                cand.telefone as candidato_telefone,
                p.titulo as pss_titulo,  
                c.nome as cargo_nome,
                c.zona as cargo_zona,
                 p.inscricao_ini as inscricao_ini2,
                    i.criado_em as inscricao_ini
                FROM pss.inscricao i
                JOIN pss.pss p ON i.pss_id = p.id
                JOIN pss.pss_cargo c ON i.pss_cargo_id = c.id
                JOIN pss.candidato cand ON i.candidato_id = cand.id
               -- JOIN pss.usuario usuario ON i.avaliador_id = usuario.id
                WHERE i.id =:inscricao";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['inscricao' => $inscricao_id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    public function busca_inscricoes_process_id_status($processo_id)
    {
        // $sql = "SELECT  Count(*)  FROM pss.inscricao   WHERE pss_id =:processo_di and status=:status";
        $sql = "SELECT 
                       SUM(CASE WHEN status IN ('apta', 'pendente') THEN 1 ELSE 0 END) AS apta,
                       SUM(CASE WHEN status = 'deferido' THEN 1 ELSE 0 END) as deferido,
                       SUM(CASE WHEN status = 'indeferido' THEN 1 ELSE 0 END) as indeferido
                FROM pss.inscricao 
                WHERE pss_id = :processo_di and status_inscricao = 'Ativa';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['processo_di' => $processo_id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function busca_pontuacao_inscricao($inscricao_id)
    {
        $sql = "SELECT 
                at2.id,
                at2.item , 
                at2.pontos ,
                at2.dt_avaliacao ,
                at2.avaliador_id ,
                u.nome AS usuario_nome
                FROM pss.avaliacao_titulo at2
                JOIN pss.usuario u 
                ON u.id = at2.avaliador_id 
                WHERE at2.inscricao_id = :inscricao_id;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['inscricao_id' => $inscricao_id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    //seta deferido e indeferido
    public static function set_inscricao_deferido_indeferido($usuario_id, $inscricao_id, $status_insc, $justificativa)
    {
        $avaliador = self::getAvaliador_Inscricao($inscricao_id);
        //VERIFICA SE O USUARIO LOGADO E O AVALIADOR SÃO IGUAIS
        // if ($avaliador != null && $usuario_id != $avaliador) {
        //     $_SESSION['erro'] = "Usuário não tem permissão para alterar.";
        //     header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        //    return;
        //}
        //SE AINDA NÃO TIVER AVALIADOR SETA O USUARIO LOGADO COMO AVALIADOR
        if (!$avaliador) {
            self::definirAvaliador_inscricao($usuario_id, $inscricao_id);
        }

        try {
            $sql = "UPDATE pss.inscricao SET status = :status_insc , motivo_cancelamento = :justificativa, avaliador_id=:avaliador WHERE id = :inscricao_id";
            $db = Database::getInstance();
            $stmt = $db->prepare($sql);
            return $stmt->execute(['status_insc' => $status_insc, 'justificativa' => $justificativa, 'inscricao_id' => $inscricao_id, 'avaliador' => $usuario_id]);
        } catch (\Throwable $th) {
            $_SESSION['erro'] = $th->getMessage();
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        }
    }
    public static function set_recurso_deferido_indeferido($usuario_id, $recurso_id, $status_insc, $justificativa)
    {
        try {
            $sql = "UPDATE pss.recurso SET status = :status_insc , motivo_decisao = :justificativa, avaliador_id=:avaliador WHERE id = :recurso_id";
            $db = Database::getInstance();
            $stmt = $db->prepare($sql);
            return $stmt->execute(['status_insc' => $status_insc, 'justificativa' => $justificativa, 'recurso_id' => $recurso_id, 'avaliador' => $usuario_id]);
        } catch (\Throwable $th) {
            $_SESSION['erro'] = $th->getMessage();
            header("Location: /dashboard/recursos/detalhes/" . $recurso_id);
        }
    }
    // insere pontuaçõa de titulo
    public static function set_inscricao_pontuacao_titulo($usuario_id, $inscricao_id, $tipo_documento, $justificativa_pontuacao, $pontos_titulo, bool $apiContext = false)
    {
        $avaliador = self::getAvaliador_Inscricao($inscricao_id);
        //VERIFICA SE O USUARIO LOGADO E O AVALIADOR SÃO IGUAIS
        // if ($avaliador != null && $usuario_id != $avaliador) {
        //     $_SESSION['erro'] = "Usuário não tem permissão para alterar.";
        //     header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        //     return;
        // }
        //SE AINDA NÃO TIVER AVALIADOR SETA O USUARIO LOGADO COMO AVALIADOR
        if (!$avaliador) {
            self::definirAvaliador_inscricao($usuario_id, $inscricao_id);
        }



        try {
            $sql = "INSERT INTO pss.avaliacao_titulo
                ( inscricao_id, item, pontos, avaliador_id, descricao,dt_avaliacao)
                VALUES( :inscricao_id, :tipo_documento, :pontos_titulo, :usuario_id,  :justificativa_pontuacao,now());";
            $db = Database::getInstance();
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'inscricao_id' => $inscricao_id,
                'tipo_documento' => $tipo_documento,
                'pontos_titulo' => $pontos_titulo,
                'usuario_id' => $usuario_id,
                'justificativa_pontuacao' => $justificativa_pontuacao

            ]);
        } catch (\Throwable $th) {
            if ($apiContext) {
                throw $th;
            }
            $_SESSION['erro'] = $th->getMessage();
            header("Location: /dashboard/inscricoes/detalhes/" . $inscricao_id);
        }
    }
    public static function getAvaliador_Inscricao($inscricao_id)
    {
        $db = Database::getInstance();
        $sql = "SELECT avaliador_id FROM pss.inscricao WHERE id = :id";
        $stmt =  $db->prepare($sql);
        $stmt->execute(['id' => $inscricao_id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->avaliador_id : null;
    }
    private static function definirAvaliador_inscricao($usuario_id, $inscricao_id)
    {
        $db = Database::getInstance();
        $sql = "UPDATE pss.inscricao SET avaliador_id = :avaliador WHERE id = :id";
        $stmt =  $db->prepare($sql);
        return $stmt->execute(['id' => $inscricao_id, 'avaliador' => $usuario_id]);
    }


    public function getInscricaoIdByPontuacaoId($pontuacao_id)
    {
        $sql = "SELECT inscricao_id FROM pss.avaliacao_titulo WHERE id = :pontuacao_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pontuacao_id' => $pontuacao_id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->inscricao_id : null;
    }

    public function excluirPontuacaoPorId($pontuacao_id, $avaliador_id)
    {
        // $sql = "DELETE FROM pss.avaliacao_titulo WHERE id = :pontuacao_id and avaliador_id=:avaliador_id";
        $sql = "DELETE FROM pss.avaliacao_titulo WHERE id = :pontuacao_id";
        $stmt = $this->db->prepare($sql);
        // return $stmt->execute(['pontuacao_id' => $pontuacao_id, 'avaliador_id' => $avaliador_id]);
        return $stmt->execute(['pontuacao_id' => $pontuacao_id]);
    }

    public function alterarDocumento($documento_tipo, $novo_arquivo)
    {
        $nomeArquivo = basename($documento_tipo);
        $diretorio = pathinfo($documento_tipo, PATHINFO_DIRNAME);
        $uploadDir = "/var/www/{$diretorio}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $novoNome = $nomeArquivo;
        $caminhoCompleto = $uploadDir . $novoNome;

        if (!move_uploaded_file($novo_arquivo['tmp_name'], $caminhoCompleto)) {
            throw new \Exception("Falha ao mover o arquivo enviado.");
        }
    }
}
