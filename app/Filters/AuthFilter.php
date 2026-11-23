<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro `auth`: exige usuário autenticado.
 *
 * Aplicado globalmente (exceto login/recuperação de senha). Se o usuário não
 * estiver logado, redireciona para `/auth/login`. Se a conta foi desativada
 * por um admin durante a sessão, encerra a sessão e redireciona com aviso.
 */
class AuthFilter implements FilterInterface
{
    /**
     * @param list<string>|null $arguments
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        // Conta desativada por um admin no meio da sessão.
        if ($session->get('active') === false) {
            $session->destroy();

            return redirect()->to('auth/login')->with('error', 'Sua conta foi desativada.');
        }

        return null;
    }

    /**
     * @param list<string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
