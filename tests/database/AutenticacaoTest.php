<?php

use App\Libraries\Autenticacao;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\Sprint7TestSeeder;

/**
 * Testes do serviço de autenticação (fluxo de login, rate limiting e
 * recuperação de senha) — Sprint 7.
 *
 * @internal
 */
final class AutenticacaoTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = Sprint7TestSeeder::class;

    private Autenticacao $auth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = new Autenticacao();
    }

    public function testTentarLoginComCredenciaisValidas(): void
    {
        $usuario = $this->auth->tentarLogin('agente@aassc.com.br', 'agente123');

        $this->assertIsArray($usuario);
        $this->assertSame('agente@aassc.com.br', $usuario['email']);
        $this->assertTrue($this->auth->estaLogado());
        $this->assertSame(20, (int) session()->get('user_id'));
    }

    public function testTentarLoginComSenhaIncorreta(): void
    {
        $this->assertFalse($this->auth->tentarLogin('agente@aassc.com.br', 'senha-errada'));
        $this->assertFalse($this->auth->estaLogado());
    }

    public function testTentarLoginComEmailInexistente(): void
    {
        $this->assertFalse($this->auth->tentarLogin('naoexiste@aassc.com.br', 'qualquer'));
    }

    public function testTentarLoginComUsuarioInativo(): void
    {
        $this->assertFalse($this->auth->tentarLogin('inativo@aassc.com.br', 'inativo123'));
    }

    public function testBloqueioAposLimiteDeTentativas(): void
    {
        $identity = 'agente@aassc.com.br';

        $this->assertFalse($this->auth->bloqueado($identity));

        for ($i = 0; $i < Autenticacao::TENTATIVAS_MAXIMAS; $i++) {
            $this->auth->registrarTentativa($identity, '127.0.0.1');
        }

        $this->assertTrue($this->auth->bloqueado($identity));

        $this->auth->limparTentativas($identity);

        $this->assertFalse($this->auth->bloqueado($identity));
    }

    public function testTrocarSenhaFluxoCompleto(): void
    {
        $this->assertTrue($this->auth->trocarSenha(20, 'agente123', 'nova-senha-123'));
        $this->assertFalse($this->auth->trocarSenha(20, 'agente123', 'outra-senha'));
        $this->assertTrue($this->auth->trocarSenha(20, 'nova-senha-123', 'outra-senha'));
    }

    public function testTrocarSenhaComSenhaAtualIncorreta(): void
    {
        $this->assertFalse($this->auth->trocarSenha(20, 'senha-errada', 'nova-senha-123'));
    }

    public function testRecuperarSenhaGeraTokenDe40Caracteres(): void
    {
        $token = $this->auth->recuperarSenha('agente@aassc.com.br');

        $this->assertIsString($token);
        $this->assertSame(40, strlen($token));
    }

    public function testRecuperarSenhaComEmailInexistenteRetornaNull(): void
    {
        $this->assertNull($this->auth->recuperarSenha('naoexiste@aassc.com.br'));
    }

    public function testResetarSenhaComTokenValido(): void
    {
        $token = $this->auth->recuperarSenha('agente@aassc.com.br');

        $this->assertTrue($this->auth->resetarSenha($token, 'senha-nova-123'));
        $this->assertIsArray($this->auth->tentarLogin('agente@aassc.com.br', 'senha-nova-123'));
    }

    public function testResetarSenhaComTokenInvalido(): void
    {
        $this->assertFalse($this->auth->resetarSenha('token-invalido', 'senha-nova-123'));
    }

    public function testGruposDoUsuario(): void
    {
        $this->assertSame(['nomes' => ['admin'], 'ids' => [1]], $this->auth->gruposDoUsuario(10));
        $this->assertSame(['nomes' => ['agente'], 'ids' => [2]], $this->auth->gruposDoUsuario(20));
    }

    public function testProximoIdGrupo(): void
    {
        $this->assertSame(5, $this->auth->proximoIdGrupo());
    }
}
