<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de fluxo de autenticação no nível HTTP (filtros de auth/group) —
 * Sprint 7.
 *
 * Foca nos fluxos GET (sem CSRF), cobrindo redirecionamentos e acesso.
 * O fluxo de login em si (credenciais, rate limiting) é coberto por
 * `AutenticacaoTest`.
 *
 * @internal
 */
final class AuthFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testPaginaDeLoginRenderiza(): void
    {
        $result = $this->get('auth/login');

        $result->assertStatus(200);
        $result->assertSee('Entrar');
    }

    public function testDashboardRedirecionaParaLoginQuandoNaoAutenticado(): void
    {
        $result = $this->get('/');

        $result->assertRedirectTo('auth/login');
    }

    public function testAreaAdminRedirecionaParaHomeParaNaoAdmin(): void
    {
        $this->withSession([
            'logged_in' => true,
            'user_id'   => 20,
            'username'  => 'agente',
            'groups'    => ['agente'],
            'active'    => true,
        ]);

        $result = $this->get('auth');

        $result->assertRedirectTo('/');
    }

    public function testLogoutRedirecionaParaLogin(): void
    {
        $this->withSession([
            'logged_in' => true,
            'user_id'   => 20,
            'username'  => 'agente',
            'groups'    => ['agente'],
            'active'    => true,
        ]);

        $result = $this->get('auth/logout');

        $result->assertRedirectTo('auth/login');
    }
}
