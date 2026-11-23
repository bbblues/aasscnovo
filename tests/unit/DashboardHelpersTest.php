<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Testes das funções de agregação dos gráficos do dashboard (Sprint 4).
 *
 * @internal
 */
final class DashboardHelpersTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('funcoes');
    }

    public function testAgruparOutrosNaoAlteraAteOitoEntradas(): void
    {
        $dados = ['a' => 3, 'b' => 2, 'c' => 1];

        $this->assertSame($dados, agrupar_outros($dados));
    }

    public function testAgruparOutrosSomaRestanteEmOutros(): void
    {
        $dados = [];
        for ($i = 1; $i <= 10; $i++) {
            $dados['cliente_' . $i] = $i;
        }
        arsort($dados); // 10..1

        $resultado = agrupar_outros($dados);

        $this->assertCount(9, $resultado);
        $this->assertArrayHasKey('outros', $resultado);
        $this->assertSame(3, $resultado['outros']);
        $this->assertArrayNotHasKey('cliente_1', $resultado);
        $this->assertArrayNotHasKey('cliente_2', $resultado);
    }

    public function testGraficoBarrasCalculaPercentuais(): void
    {
        $resultado = grafico_barras(['a' => 1, 'b' => 3]);

        $this->assertSame(75.0, $resultado['b']);
        $this->assertSame(25.0, $resultado['a']);
    }

    public function testGraficoBarrasAbsolutoMantemValores(): void
    {
        $resultado = grafico_barras_absoluto(['a' => 5, 'b' => 3]);

        $this->assertSame(5, $resultado['a']);
        $this->assertSame(3, $resultado['b']);
    }

    public function testGraficoBarrasComTotalZero(): void
    {
        $resultado = grafico_barras([]);

        $this->assertSame([], $resultado);
    }
}
