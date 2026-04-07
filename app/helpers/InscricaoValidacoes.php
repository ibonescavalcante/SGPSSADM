<?php

namespace App\controllers;

use App\models\Pss;
use App\models\Inscricao;

/**
 * Classe auxiliar para validações relacionadas a inscrições
 */
class InscricaoValidacoes
{
    /**
     * Verifica se o período de inscrições está aberto para um PSS
     * 
     * @param int $pss_id ID do PSS
     * @return array ['valido' => bool, 'mensagem' => string, 'tipo' => string, 'data_inicio' => string, 'data_fim' => string]
     */
    public static function validarPeriodoInscricao($pss_id)
    {
        $pss = Pss::buscarPorId($pss_id);
        
        if (!$pss) {
            return [
                'valido' => false,
                'mensagem' => 'PSS não encontrado',
                'tipo' => 'erro',
                'data_inicio' => null,
                'data_fim' => null
            ];
        }

        // Configurar timezone brasileiro para todas as comparações
        $timezone_brasil = new \DateTimeZone('America/Sao_Paulo');
        $agora = new \DateTime('now', $timezone_brasil);
        
        // Criar objetos DateTime com timezone brasileiro
        $inicio = new \DateTime($pss["inscricao_ini"]);
        $inicio->setTimezone($timezone_brasil);
        
        $fim = new \DateTime($pss["inscricao_fim"]);
        $fim->setTimezone($timezone_brasil);

        if ($agora < $inicio) {
            return [
                'valido' => false,
                'mensagem' => 'O período de inscrições ainda não iniciou.',
                'tipo' => 'periodo_nao_iniciado',
                'data_inicio' => $pss["inscricao_ini"],
                'data_fim' => $pss["inscricao_fim"],
                'data_inicio_formatada' => $inicio->format('d/m/Y H:i'),
                'data_fim_formatada' => $fim->format('d/m/Y H:i'),
                'pss_titulo' => $pss["titulo"] ?? 'PSS'
            ];
        }

        if ($agora > $fim) {
            return [
                'valido' => false,
                'mensagem' => 'O período de inscrições foi encerrado.',
                'tipo' => 'periodo_encerrado',
                'data_inicio' => $pss["inscricao_ini"],
                'data_fim' => $pss["inscricao_fim"],
                'data_inicio_formatada' => $inicio->format('d/m/Y H:i'),
                'data_fim_formatada' => $fim->format('d/m/Y H:i'),
                'pss_titulo' => $pss["titulo"] ?? 'PSS'
            ];
        }

        return [
            'valido' => true,
            'mensagem' => 'Período de inscrições aberto',
            'tipo' => 'periodo_ativo',
            'data_inicio' => $pss["inscricao_ini"],
            'data_fim' => $pss["inscricao_fim"],
            'data_inicio_formatada' => $inicio->format('d/m/Y H:i'),
            'data_fim_formatada' => $fim->format('d/m/Y H:i'),
            'pss_titulo' => $pss["titulo"] ?? 'PSS'
        ];
    }

    /**
     * Verifica se um candidato pode editar seus dados pessoais
     * Regra: Não pode editar se tiver inscrições ativas em PSS com período aberto
     * 
     * @param int $candidato_id ID do candidato
     * @return array ['pode_editar' => bool, 'mensagem' => string]
     */
    public static function validarEdicaoDadosPessoais($candidato_id)
    {
        try {
            // Buscar inscrições ativas do candidato
            $inscricoes = Inscricao::buscarPorCandidato($candidato_id);
            
            if (empty($inscricoes)) {
                return [
                    'pode_editar' => true,
                    'mensagem' => 'Nenhuma inscrição ativa encontrada'
                ];
            }

            // Configurar timezone brasileiro
            $timezone_brasil = new \DateTimeZone('America/Sao_Paulo');
            $agora = new \DateTime('now', $timezone_brasil);
            $pss_com_periodo_aberto = [];

            foreach ($inscricoes as $inscricao) {
                // Verificar se a inscrição está ativa e APTA (não cancelada e status 'APTA')
                if (!isset($inscricao['status']) || $inscricao['status'] === 'cancelada' || $inscricao['status'] !== 'APTA') {
                    continue;
                }

                $pss = Pss::buscarPorId($inscricao['pss_id']);
                if (!$pss) {
                    continue;
                }

                // Criar objetos DateTime com timezone brasileiro
                $inicio = new \DateTime($pss["inscricao_ini"]);
                $inicio->setTimezone($timezone_brasil);
                
                $fim = new \DateTime($pss["inscricao_fim"]);
                $fim->setTimezone($timezone_brasil);

                // A edição é bloqueada se o PSS estiver 'em_andamento', o período de inscrição já tiver terminado
                // E a inscrição do candidato para este PSS estiver com status 'APTA'.
                if ($pss['status_global'] === 'em_andamento' && $agora > $fim && $inscricao['status'] === 'APTA') {
                    $pss_com_periodo_aberto[] = $pss['titulo'] ?? 'PSS ID: ' . $pss['id'];
                }
            }

            if (!empty($pss_com_periodo_aberto)) {
                return [
                    'pode_editar' => false,
                    'mensagem' => 'Não é possível editar dados pessoais durante período de inscrição ativo nos seguintes PSS: ' . implode(', ', $pss_com_periodo_aberto)
                ];
            }

            return [
                'pode_editar' => true,
                'mensagem' => 'Edição de dados pessoais permitida'
            ];

        } catch (\Exception $e) {
            error_log("Erro ao validar edição de dados pessoais: " . $e->getMessage());
            return [
                'pode_editar' => false,
                'mensagem' => 'Erro interno ao verificar permissões de edição'
            ];
        }
    }

    /**
     * Verifica se um candidato já possui inscrição em um PSS
     * 
     * @param int $pss_id ID do PSS
     * @param int $candidato_id ID do candidato
     * @return array ['ja_inscrito' => bool, 'inscricao' => array|null]
     */
    public static function verificarInscricaoExistente($pss_id, $candidato_id)
    {
        try {
            $inscricao_existente = Inscricao::verificarInscricaoExistente($pss_id, $candidato_id);
            
            if ($inscricao_existente) {
                return [
                    'ja_inscrito' => true,
                    'inscricao' => $inscricao_existente
                ];
            }

            return [
                'ja_inscrito' => false,
                'inscricao' => null
            ];

        } catch (\Exception $e) {
            error_log("Erro ao verificar inscrição existente: " . $e->getMessage());
            return [
                'ja_inscrito' => false,
                'inscricao' => null
            ];
        }
    }

    /**
     * Valida se uma inscrição pode ser processada
     * Combina todas as validações necessárias
     * 
     * @param int $pss_id ID do PSS
     * @param int $candidato_id ID do candidato
     * @return array ['valido' => bool, 'mensagem' => string, 'redirect_url' => string|null]
     */
    public static function validarProcessamentoInscricao($pss_id, $candidato_id)
    {
        // 1. Verificar período de inscrição
        $validacao_periodo = self::validarPeriodoInscricao($pss_id);
        if (!$validacao_periodo['valido']) {
            return [
                'valido' => false,
                'mensagem' => $validacao_periodo['mensagem'],
                'redirect_url' => '/?erro=' . urlencode($validacao_periodo['mensagem'])
            ];
        }

        // 2. Verificar inscrição existente
        $validacao_inscricao = self::verificarInscricaoExistente($pss_id, $candidato_id);
        if ($validacao_inscricao['ja_inscrito']) {
            $inscricao_completa = Inscricao::buscarPorId($validacao_inscricao['inscricao']['id']);
            if ($inscricao_completa && !empty($inscricao_completa['protocolo'])) {
                return [
                    'valido' => false,
                    'mensagem' => 'Você já possui inscrição neste PSS',
                    'redirect_url' => '/inscricao/comprovante/' . $inscricao_completa['protocolo']
                ];
            } else {
                return [
                    'valido' => false,
                    'mensagem' => 'Você já possui inscrição neste PSS',
                    'redirect_url' => '/painel?erro=' . urlencode('Você já possui inscrição neste PSS')
                ];
            }
        }

        return [
            'valido' => true,
            'mensagem' => 'Inscrição pode ser processada',
            'redirect_url' => null
        ];
    }
}
