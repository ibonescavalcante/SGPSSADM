<?php

namespace App\models;

use App\core\Database;
use PDO;

class TipoDocumento
{
    /**
     * Código da inscrição -> valor enviado à API /api/avaliacao-titulo (campo tipo_documento).
     * Apenas títulos que tinham data-tipo-documento na view antiga.
     */
    public const CODIGO_PARA_PONTUACAO_API = [
        'comprovante_experiencia_declaracoes' => 'Experiencia',
        'certificados' => 'Certificado',
        'POS_GRADUACAO_LATU_SENSU' => 'Posgraduacao',
        'CUR_EXTENSAO' => 'Extensao',
    ];

    /**
     * Chaves de ficheiro fora de extra_json.documentos (raiz do JSON).
     */
    public const CODIGOS_RAIZ_EXTRA_JSON = [
        'laudo_pcd',
    ];

    public static function buscarTodos(): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                'SELECT id, codigo, tipo::text AS tipo, nome, descricao, multiplos_arquivos
                 FROM pss.tipos_documento
                 WHERE ativo = TRUE
                 ORDER BY tipo::text, nome'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            error_log('TipoDocumento::buscarTodos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista para ecrã de detalhes da inscrição (requisitos / títulos).
     *
     * @return list<array{id:int,codigo:string,tipo:string,nome:string,descricao:?string,multiplos_arquivos:bool}>
     */
    public static function listarParaDetalheInscricao(): array
    {
        $rows = self::buscarTodos();
        $out = [];
        foreach ($rows as $r) {
            if (!is_array($r)) {
                continue;
            }
            $r['id'] = (int) ($r['id'] ?? 0);
            $r['codigo'] = (string) ($r['codigo'] ?? '');
            $r['tipo'] = strtolower(trim((string) ($r['tipo'] ?? '')));
            $r['nome'] = (string) ($r['nome'] ?? $r['codigo']);
            $r['descricao'] = isset($r['descricao']) && $r['descricao'] !== null ? (string) $r['descricao'] : null;
            $m = $r['multiplos_arquivos'] ?? false;
            $r['multiplos_arquivos'] = $m === true || $m === 't' || $m === '1' || $m === 1;
            if ($r['codigo'] !== '') {
                $out[] = $r;
            }
        }
        return $out;
    }

    /**
     * @return array<string, array{id:int,codigo:string,tipo:string,nome:string,descricao:?string,multiplos_arquivos:bool}>
     */
    public static function mapaPorCodigo(): array
    {
        $map = [];
        foreach (self::listarParaDetalheInscricao() as $row) {
            $map[$row['codigo']] = $row;
        }
        return $map;
    }

    /**
     * requisito | titulo (aceita na BD: requisito, requisitos, titulo, titulos, etc.)
     */
    public static function tipoParaSecao(string $tipoDb): string
    {
        $t = strtolower(trim($tipoDb));
        return str_contains($t, 'titulo') ? 'titulo' : 'requisito';
    }

    public static function codigoParaPontuacaoApi(string $codigo): ?string
    {
        return self::CODIGO_PARA_PONTUACAO_API[$codigo] ?? null;
    }

    /**
     * Indica se o valor guardado no JSON representa anexo(s) presente(s).
     */
    public static function valorTemAnexo($valor): bool
    {
        if ($valor === null || $valor === '') {
            return false;
        }
        if (is_array($valor)) {
            foreach ($valor as $x) {
                if ($x !== null && $x !== '' && $x !== []) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Lista códigos que têm ficheiro na inscrição, percorrendo o JSON:
     * primeiro `documentos` (todas as chaves com valor), depois chaves na raiz em CODIGOS_RAIZ_EXTRA_JSON.
     *
     * @return list<string>
     */
    public static function codigosComAnexoNaInscricao(array $extraJson, array $documentos): array
    {
        $vistos = [];
        $out = [];
        foreach ($documentos as $codigo => $valor) {
            $cod = (string) $codigo;
            if ($cod === '' || !self::valorTemAnexo($valor)) {
                continue;
            }
            $vistos[$cod] = true;
            $out[] = $cod;
        }
        foreach (self::CODIGOS_RAIZ_EXTRA_JSON as $codigo) {
            if (!array_key_exists($codigo, $extraJson)) {
                continue;
            }
            if (!self::valorTemAnexo($extraJson[$codigo])) {
                continue;
            }
            if (!isset($vistos[$codigo])) {
                $vistos[$codigo] = true;
                $out[] = $codigo;
            }
        }

        return $out;
    }

    /**
     * Metadados da tabela para um código, ou fallback mínimo se o JSON tiver chave não cadastrada.
     *
     * @return array{codigo:string,tipo:string,nome:string,descricao:?string,multiplos_arquivos:bool}
     */
    public static function metaParaCodigo(string $codigo, array $mapaPorCodigo): array
    {
        if (isset($mapaPorCodigo[$codigo]) && is_array($mapaPorCodigo[$codigo])) {
            $r = $mapaPorCodigo[$codigo];
            return [
                'codigo' => (string) ($r['codigo'] ?? $codigo),
                'tipo' => (string) ($r['tipo'] ?? 'requisito'),
                'nome' => (string) ($r['nome'] ?? $codigo),
                'descricao' => isset($r['descricao']) && $r['descricao'] !== null ? (string) $r['descricao'] : null,
                'multiplos_arquivos' => (bool) ($r['multiplos_arquivos'] ?? false),
            ];
        }

        return [
            'codigo' => $codigo,
            'tipo' => 'requisito',
            'nome' => $codigo,
            'descricao' => null,
            'multiplos_arquivos' => false,
        ];
    }

    /**
     * Obtém caminho(s) anexados: documentos[codigo] ou raiz do extra_json (ex.: laudo_pcd).
     *
     * @return string|list<string>|null
     */
    public static function valorAnexoInscricao(array $extraJson, array $documentos, string $codigo)
    {
        if (in_array($codigo, self::CODIGOS_RAIZ_EXTRA_JSON, true) && array_key_exists($codigo, $extraJson)) {
            return $extraJson[$codigo];
        }
        return $documentos[$codigo] ?? null;
    }

    /**
     * @param string|list<string>|null $raw
     * @return list<string>
     */
    public static function normalizarListaCaminhos($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_array($raw)) {
            $list = [];
            foreach ($raw as $item) {
                if ($item === null || $item === '') {
                    continue;
                }
                $list[] = str_replace('/var/www/', '/', (string) $item);
            }
            return $list;
        }
        return [str_replace('/var/www/', '/', (string) $raw)];
    }
}
