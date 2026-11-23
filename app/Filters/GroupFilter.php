<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro `group`: exige que o usuário pertença a um dos grupos informados.
 *
 * Uso no filtro: `group:admin` ou `group:admin,agente` — a lista após ":" é
 * recebida em `$arguments` (separada por vírgula). A checagem é feita contra
 * os nomes de grupo gravados na sessão no login.
 */
class GroupFilter implements FilterInterface
{
    /**
     * @param list<string>|null $arguments
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $grupos = (array) session()->get('groups');

        foreach ((array) $arguments as $grupo) {
            if (in_array($grupo, $grupos, true)) {
                return null;
            }
        }

        return redirect()->to('/')->with('error', 'Acesso negado: você não possui permissão para esta área.');
    }

    /**
     * @param list<string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
