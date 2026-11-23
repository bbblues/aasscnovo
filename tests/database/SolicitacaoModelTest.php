<?php

use App\Models\Solicitacao;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\Sprint7TestSeeder;

/**
 * Testes do Model Solicitacao (consultas, filtros e cadastro/edição de dados)
 * — Sprint 7.
 *
 * @internal
 */
final class SolicitacaoModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = Sprint7TestSeeder::class;

    private Solicitacao $solicitacoes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->solicitacoes = new Solicitacao();
    }

    public function testConsultarRetornaOrdenadasPorCriadoDesc(): void
    {
        $solicitacoes = $this->solicitacoes->consultar([], 20, 0);

        $this->assertCount(3, $solicitacoes);
        $this->assertSame('Pedido de consulta C', $solicitacoes[0]['pedido']);
    }

    public function testContarComEComApenasAbertas(): void
    {
        $this->assertSame(3, $this->solicitacoes->contar([]));
        $this->assertSame(2, $this->solicitacoes->contar([], true));
    }

    public function testConsultarApenasAbertas(): void
    {
        $abertas = $this->solicitacoes->consultar([], 20, 0, true);

        $this->assertCount(2, $abertas);

        foreach ($abertas as $solicitacao) {
            $this->assertNotSame(3, (int) $solicitacao['sol_sit_id']);
        }
    }

    public function testDaPessoaRetornaSolicitacoes(): void
    {
        $solicitacoes = $this->solicitacoes->daPessoa(100);

        $this->assertCount(1, $solicitacoes);
        $this->assertSame('Pedido de consulta A', $solicitacoes[0]['pedido']);
    }

    public function testSolPorAgenteAberta(): void
    {
        $abertas = $this->solicitacoes->solPorAgenteAberta(20);

        $this->assertCount(2, $abertas);
    }

    public function testAberturasParaRelatorio(): void
    {
        $aberturas = $this->solicitacoes->aberturas([]);

        $this->assertCount(3, $aberturas);
        $this->assertSame('Abertura de solicitação', $aberturas[0]['atividade']);
    }

    public function testFiltroPorResponsavel(): void
    {
        $solicitacoes = $this->solicitacoes->consultar(['responsavel' => 'Ana'], 20, 0);

        $this->assertCount(1, $solicitacoes);
        $this->assertSame('Ana', $solicitacoes[0]['responsavel']);
    }

    public function testFiltroPorPeriodo(): void
    {
        $solicitacoes = $this->solicitacoes->consultar(
            ['data_inicio' => '2026-02-01', 'data_fim' => '2026-02-28'],
            20,
            0,
        );

        $this->assertCount(1, $solicitacoes);
        $this->assertSame('Pedido de exame B', $solicitacoes[0]['pedido']);
    }
}
