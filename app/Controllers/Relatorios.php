<?php

namespace App\Controllers;

use App\Controllers\Concerns\ManipulaPessoa;
use App\Models\Comentario;
use App\Models\Solicitacao;
use App\Models\Usuario;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Relatório de atividades (Sprint 6).
 *
 * Mescla comentários + aberturas de solicitação e ordena por `created`
 * (via `usort`, helper `mesclar_atividades`).
 */
class Relatorios extends BaseController
{
    use ManipulaPessoa;

    protected Comentario $comentarios;

    protected Solicitacao $solicitacoes;

    protected Usuario $usuarios;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url', 'funcoes']);

        $this->comentarios  = new Comentario();
        $this->solicitacoes = new Solicitacao();
        $this->usuarios     = new Usuario();
    }

    public function index()
    {
        $chave = 'filtros_relatorios';

        if ($this->request->getMethod() === 'POST') {
            session()->set($chave, $this->lerFiltrosDoPost());

            return redirect()->to('relatorios');
        }

        $filtros = session()->get($chave);
        if (! is_array($filtros)) {
            $filtros = [];
        }

        $atividades = mesclar_atividades(
            $this->comentarios->ultimasAtualizacoes($filtros),
            $this->solicitacoes->aberturas($filtros)
        );

        return view('relatorios/index', [
            'title'      => 'Relatório de Atividades',
            'atividades' => $atividades,
            'filtros'    => $filtros,
            'clientes'   => $this->usuarios->doGrupo(4),
        ]);
    }

    public function limpar()
    {
        session()->remove('filtros_relatorios');

        return redirect()->to('relatorios');
    }
}
