<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Testes das funções utilitárias usadas na Sprint 5 (Pessoas + Solicitações).
 *
 * @internal
 */
final class FuncoesHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('funcoes');
    }

    public function testSituacaoByIdMapeiaIdsConhecidos(): void
    {
        $this->assertSame('Aguardando Resp', situacao_by_id(1));
        $this->assertSame('Em Andamento', situacao_by_id(2));
        $this->assertSame('Finalizado', situacao_by_id(3));
        $this->assertSame('', situacao_by_id(99));
    }

    public function testDataFinalSomaUmDia(): void
    {
        $this->assertSame('2026-01-01', data_final('2025-12-31'));
    }

    public function testDataFinalVaziaRetornaVazia(): void
    {
        $this->assertSame('', data_final(''));
    }

    public function testRemoverValoresVaziosPreservaZero(): void
    {
        $dados = ['a' => '', 'b' => null, 'c' => 0, 'd' => '0', 'e' => 'valor'];

        remover_valores_vazios($dados);

        $this->assertSame(['c' => 0, 'd' => '0', 'e' => 'valor'], $dados);
    }

    public function testSeparaPostDividePessoaTelefoneSolicitacao(): void
    {
        $post = [
            'pes_nome'              => 'João da Silva',
            'pes_cidade'            => 'Araraquara',
            'email'                 => 'joao@example.com',
            'field_telefones_value' => '16 99999-0000',
            'pedido'                => 'Solicitação de consulta',
            'responsavel'           => 'Ana',
            'sol_sit_id'            => '2',
        ];

        $separado = separa_post($post);

        $this->assertSame('João da Silva', $separado['pessoa']['pes_nome']);
        $this->assertSame('Araraquara', $separado['pessoa']['pes_cidade']);
        $this->assertArrayNotHasKey('pedido', $separado['pessoa']);
        $this->assertSame('16 99999-0000', $separado['telefone']['field_telefones_value']);
        $this->assertSame('Solicitação de consulta', $separado['solicitacao']['pedido']);
        $this->assertSame('Ana', $separado['solicitacao']['responsavel']);
        $this->assertSame('2', $separado['solicitacao']['sol_sit_id']);
    }

    public function testSimpleLowerRemoveAcentos(): void
    {
        $this->assertSame('joo', simple_lower('João'));
        $this->assertSame('caio', simple_lower('Caio'));
        $this->assertSame('', simple_lower(null));
    }

    public function testSituacaoByIdAceitaStringNumerica(): void
    {
        $this->assertSame('Em Andamento', situacao_by_id('2'));
        $this->assertSame('Finalizado', situacao_by_id('3'));
    }

    public function testConfigPaginatorRetorna20PorPagina(): void
    {
        $config = config_paginator(100, 'pessoas');

        $this->assertSame(100, $config['total_rows']);
        $this->assertSame(20, $config['per_page']);
        $this->assertSame('pessoas', $config['base_url']);
    }
}
