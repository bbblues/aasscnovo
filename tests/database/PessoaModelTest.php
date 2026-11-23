<?php

use App\Models\Pessoa;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\Sprint7TestSeeder;

/**
 * Testes do Model Pessoa (consultas e filtros) — Sprint 7.
 *
 * @internal
 */
final class PessoaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = Sprint7TestSeeder::class;

    private Pessoa $pessoas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pessoas = new Pessoa();
    }

    public function testConsultarRetornaTodasOrdenadasPorNome(): void
    {
        $pessoas = $this->pessoas->consultar([], 20, 0);

        $this->assertCount(3, $pessoas);
        $this->assertSame('Ana Souza', $pessoas[0]['pes_nome']);
        $this->assertSame('Bruno Lima', $pessoas[1]['pes_nome']);
        $this->assertSame('Carla Mendes', $pessoas[2]['pes_nome']);
    }

    public function testContarRetornaTotal(): void
    {
        $this->assertSame(3, $this->pessoas->contar([]));
    }

    public function testFiltroPorPessoa(): void
    {
        $pessoas = $this->pessoas->consultar(['pessoa' => 'Ana'], 20, 0);

        $this->assertCount(1, $pessoas);
        $this->assertSame('Ana Souza', $pessoas[0]['pes_nome']);
    }

    public function testFiltroPorMunicipio(): void
    {
        $pessoas = $this->pessoas->consultar(['municipio' => 'Araraquara'], 20, 0);

        $this->assertCount(2, $pessoas);
    }

    public function testFiltroPorCliente(): void
    {
        $pessoas = $this->pessoas->consultar(['cliente' => '30'], 20, 0);

        $this->assertCount(3, $pessoas);
    }

    public function testFiltroPorPeriodo(): void
    {
        $pessoas = $this->pessoas->consultar(
            ['data_inicio' => '2026-02-01', 'data_fim' => '2026-02-28'],
            20,
            0,
        );

        $this->assertCount(1, $pessoas);
        $this->assertSame('Bruno Lima', $pessoas[0]['pes_nome']);
    }

    public function testConsultarAplicaPaginacao(): void
    {
        $pessoas = $this->pessoas->consultar([], 1, 0);

        $this->assertCount(1, $pessoas);
        $this->assertSame('Ana Souza', $pessoas[0]['pes_nome']);
    }
}
