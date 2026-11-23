<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Testes do relatório de atividades (Sprint 6).
 *
 * @internal
 */
final class RelatorioAtividadesTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('funcoes');
    }

    public function testMesclarAtividadesOrdenaPorCreatedDesc(): void
    {
        $comentarios = [
            ['atividade' => 'Comentário 1', 'created' => '1000', 'nome' => 'Ana', 'pedido' => 'P1'],
            ['atividade' => 'Comentário 2', 'created' => '3000', 'nome' => 'Ana', 'pedido' => 'P1'],
        ];
        $aberturas = [
            ['atividade' => 'Abertura de solicitação', 'created' => '2000', 'nome' => 'Bia', 'pedido' => 'P2'],
        ];

        $resultado = mesclar_atividades($comentarios, $aberturas);

        $this->assertCount(3, $resultado);
        $this->assertSame('3000', $resultado[0]['created']);
        $this->assertSame('2000', $resultado[1]['created']);
        $this->assertSame('1000', $resultado[2]['created']);
    }

    public function testMesclarAtividadesVazioRetornaVazio(): void
    {
        $this->assertSame([], mesclar_atividades([], []));
    }
}
