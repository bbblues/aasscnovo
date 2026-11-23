<?php

use App\Libraries\Autenticacao;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Cookie;
use Config\Filters;
use Config\Security;

/**
 * Verifica as configurações de segurança revisadas na Sprint 7 (CSRF,
 * sessão/cookies e rate limiting) como testes automatizados.
 *
 * @internal
 */
final class SegurancaConfigTest extends CIUnitTestCase
{
    public function testCsrfHabilitadoGlobalmente(): void
    {
        $security = config(Security::class);
        $filters  = config(Filters::class);

        $this->assertSame('cookie', $security->csrfProtection);
        $this->assertContains('csrf', $filters->globals['before']);
    }

    public function testCookiesHttpOnly(): void
    {
        $cookie = config(Cookie::class);

        $this->assertTrue($cookie->httponly);
        $this->assertTrue($cookie->secure === (ENVIRONMENT === 'production'));
    }

    public function testFiltroAuthPermiteApenasRotasPublicas(): void
    {
        $filters = config(Filters::class);

        $except = $filters->globals['before']['auth']['except'] ?? [];

        $this->assertContains('auth/login', $except);
        $this->assertContains('auth/forgot_password', $except);
    }

    public function testRateLimitingConfigurado(): void
    {
        $this->assertSame(5, Autenticacao::TENTATIVAS_MAXIMAS);
        $this->assertSame(15, Autenticacao::JANELA_MINUTOS);
        $this->assertSame(3600, Autenticacao::EXPIRACAO_RESET);
    }
}
