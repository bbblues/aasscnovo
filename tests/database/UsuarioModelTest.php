<?php

use App\Models\Usuario;
use App\Models\UsuarioGrupo;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\Sprint7TestSeeder;

/**
 * Testes dos Models de usuários e grupos — Sprint 7.
 *
 * @internal
 */
final class UsuarioModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = Sprint7TestSeeder::class;

    private Usuario $usuarios;

    private UsuarioGrupo $usuariosGrupos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarios       = new Usuario();
        $this->usuariosGrupos = new UsuarioGrupo();
    }

    public function testBuscarPorEmail(): void
    {
        $usuario = $this->usuarios->buscarPorEmail('agente@aassc.com.br');

        $this->assertIsArray($usuario);
        $this->assertSame(20, (int) $usuario['id']);
    }

    public function testBuscarPorEmailInexistenteRetornaNull(): void
    {
        $this->assertNull($this->usuarios->buscarPorEmail('naoexiste@aassc.com.br'));
    }

    public function testDoGrupoRetornaSomenteAtivos(): void
    {
        $clientes = $this->usuarios->doGrupo(4);

        $this->assertCount(1, $clientes);
        $this->assertSame(30, (int) $clientes[0]['id']);
    }

    public function testGruposDoUsuario(): void
    {
        $grupos = $this->usuariosGrupos->gruposDoUsuario(10);

        $this->assertCount(1, $grupos);
        $this->assertSame('admin', $grupos[0]['name']);
    }
}
