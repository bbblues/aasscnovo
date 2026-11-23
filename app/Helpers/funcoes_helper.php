<?php

/**
 * funcoes_helper.php — funções utilitárias portadas do CI3.
 *
 * Carregado sob demanda com: helper('funcoes').
 */

if (! function_exists('config_paginator')) {
    /**
     * Configuração centralizada de paginação (20 registros por página, links Bootstrap).
     *
     * No CI4 a paginação é feita por `Model::paginate($perPage)` + `Pager`.
     * Esta função mantém uma fonte única para os parâmetros usados nas listagens.
     */
    function config_paginator(int $total, string $baseUrl): array
    {
        return [
            'base_url'   => $baseUrl,
            'total_rows' => $total,
            'per_page'   => 20,
            'attributes' => ['class' => 'page-link'],
        ];
    }
}

if (! function_exists('grafico_barras')) {
    /**
     * Converte contagens em percentuais, ordena desc e agrupa em "outros" (>8).
     *
     * @param array<string, int|float> $array ['label' => valor]
     *
     * @return array<string, float>
     */
    function grafico_barras(array $array): array
    {
        $total = array_sum($array);

        $percentuais = [];
        foreach ($array as $label => $valor) {
            $percentuais[$label] = ($total > 0) ? round(($valor / $total) * 100, 2) : 0.0;
        }

        arsort($percentuais);

        return agrupar_outros($percentuais);
    }
}

if (! function_exists('grafico_barras_absoluto')) {
    /**
     * Mantém valores absolutos, ordena desc e agrupa em "outros" (>8).
     *
     * @param array<string, int|float> $array ['label' => valor]
     *
     * @return array<string, int|float>
     */
    function grafico_barras_absoluto(array $array): array
    {
        arsort($array);

        return agrupar_outros($array);
    }
}

if (! function_exists('agrupar_outros')) {
    /**
     * Mantém as 8 maiores entradas e soma o restante na chave "outros".
     *
     * @param array<string, int|float> $array
     *
     * @return array<string, int|float>
     */
    function agrupar_outros(array $array): array
    {
        if (count($array) <= 8) {
            return $array;
        }

        $slice            = array_slice($array, 0, 8, true);
        $slice['outros']  = array_sum(array_slice($array, 8));

        return $slice;
    }
}

if (! function_exists('situacao_by_id')) {
    /**
     * Mapeia o id da situação (sol_sit_id) para o texto exibido.
     */
    function situacao_by_id($id): string
    {
        return match ((int) $id) {
            1 => 'Aguardando Resp',
            2 => 'Em Andamento',
            3 => 'Finalizado',
            default => '',
        };
    }
}

if (! function_exists('data_final')) {
    /**
     * Soma +1 dia à data (usado para tornar o fim do período inclusivo).
     */
    function data_final(string $data): string
    {
        if ($data === '') {
            return '';
        }

        return date('Y-m-d', strtotime($data . ' +1 day'));
    }
}

if (! function_exists('remover_valores_vazios')) {
    /**
     * Remove filtros vazios ('' ou null) do array, preservando 0 e '0'.
     */
    function remover_valores_vazios(array &$array): void
    {
        $array = array_filter($array, static function ($valor): bool {
            return $valor !== '' && $valor !== null;
        });
    }
}

if (! function_exists('separa_post')) {
    /**
     * Divide o POST em pessoa / telefone / solicitação.
     *
     * Convenção: os campos do formulário usam os nomes das colunas do banco.
     *
     * @return array{pessoa: array<string, mixed>, telefone: array<string, mixed>, solicitacao: array<string, mixed>}
     */
    function separa_post(array $post): array
    {
        $camposPessoa = [
            'pes_nome', 'pes_cidade', 'endereco', 'cep', 'nascimento', 'rg',
            'cpf', 'sus', 'mae', 'email', 'sexo', 'pes_cliente',
            'pes_catpessoa', 'uc', 'pes_user', 'pes_pub',
        ];

        $camposTelefone = ['field_telefones_value'];

        $camposSolicitacao = [
            'spessoa', 'sagente', 'sol_sit_id', 'pedido', 'procedimento',
            'responsavel', 'obs', 'situacao', 'criado', 'created',
        ];

        return [
            'pessoa'      => array_intersect_key($post, array_flip($camposPessoa)),
            'telefone'    => array_intersect_key($post, array_flip($camposTelefone)),
            'solicitacao' => array_intersect_key($post, array_flip($camposSolicitacao)),
        ];
    }
}

if (! function_exists('simple_lower')) {
    /**
     * Normaliza um nome para o nome de arquivo do avatar.
     *
     * Converte para minúsculas e remove caracteres acentuados (ex.: "João" -> "joo"),
     * comportamento compatível com os avatares existentes em
     * `public/assets/img/avatares/*.jpg`.
     */
    function simple_lower(?string $texto): string
    {
        if ($texto === null || $texto === '') {
            return '';
        }

        $texto = mb_strtolower($texto, 'UTF-8');

        $acentos = [
            'á', 'à', 'â', 'ã', 'ä',
            'é', 'è', 'ê', 'ë',
            'í', 'ì', 'î', 'ï',
            'ó', 'ò', 'ô', 'õ', 'ö',
            'ú', 'ù', 'û', 'ü',
            'ç', 'ñ',
        ];

        return str_replace($acentos, '', $texto);
    }
}

if (! function_exists('mesclar_atividades')) {
    /**
     * Mescla comentários e aberturas de solicitação do relatório de atividades,
     * ordenando por `created` (timestamp) de forma decrescente.
     *
     * @param list<array<string, mixed>> $comentarios
     * @param list<array<string, mixed>> $aberturas
     *
     * @return list<array<string, mixed>>
     */
    function mesclar_atividades(array $comentarios, array $aberturas): array
    {
        $atividades = array_merge($comentarios, $aberturas);

        usort($atividades, static function (array $a, array $b): int {
            return (int) ($b['created'] ?? 0) <=> (int) ($a['created'] ?? 0);
        });

        return $atividades;
    }
}
