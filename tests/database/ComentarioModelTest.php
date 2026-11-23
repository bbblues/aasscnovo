<?php

use App\Models\Comentario;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\Sprint7TestSeeder;

/**
 * Testes do Model Comentario (consultas e filtros) — Sprint 7.
 *
 * @internal
 */
final class ComentarioModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = Sprint7TestSeeder::class;

    private Comentario $comentarios;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comentarios = new Comentario();
    }

    public function testConsultarRetornaOrdenadosPorChangedDesc(): void
    {
        $comentarios = $this->comentarios->consultar([], 20, 0);

        $this->assertCount(3, $comentarios);
        $this->assertSame('Comentário C', $comentarios[0]['subject']);
    }

    public function testContar(): void
    {
        $this->assertSame(3, $this->comentarios->contar([]));
    }

    public function testDaSolicitacaoOrdenadosPorCreatedAsc(): void
    {
        $comentarios = $this->comentarios->daSolicitacao(1000);

        $this->assertCount(2, $comentarios);
        $this->assertSame('Comentário A', $comentarios[0]['subject']);
        $this->assertSame('Comentário B', $comentarios[1]['subject']);
    }

    public function testUltimasAtualizacoes(): void
    {
        $atualizacoes = $this->comentarios->ultimasAtualizacoes([]);

        $this->assertCount(3, $atualizacoes);
    }

    public function testFiltroPorPessoa(): void
    {
        $comentarios = $this->comentarios->consultar(['pessoa' => 'Bruno'], 20, 0);

        $this->assertCount(1, $comentarios);
        $this->assertSame('Comentário C', $comentarios[0]['subject']);
    }
}
