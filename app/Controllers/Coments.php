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
 * Comentários / Atualizações (Sprint 6).
 *
 * Listagem (`/coments`), adição (`/coments/add/{pessoa}`) e edição
 * (`/coments/update/{cid}`) — esta última restrita ao autor.
 */
class Coments extends BaseController
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
        $chave = 'filtros_coments';

        if ($this->request->getMethod() === 'POST') {
            session()->set($chave, $this->lerFiltrosDoPost());

            return redirect()->to('coments');
        }

        $filtros = session()->get($chave);
        if (! is_array($filtros)) {
            $filtros = [];
        }

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 20;

        $total       = $this->comentarios->contar($filtros);
        $comentarios = $this->comentarios->consultar($filtros, $perPage, ($page - 1) * $perPage);

        $links = service('pager')->makeLinks($page, $perPage, $total, 'default_full');

        return view('coments/index', [
            'title'       => 'Comentários',
            'comentarios' => $comentarios,
            'filtros'     => $filtros,
            'clientes'    => $this->usuarios->doGrupo(4),
            'links'       => $links,
            'total'       => $total,
        ]);
    }

    public function limpar()
    {
        session()->remove('filtros_coments');

        return redirect()->to('coments');
    }

    /**
     * Adiciona um comentário à solicitação informada em `nid` (sid).
     *
     * `{pessoa}` é usado apenas para redirecionar de volta ao atendimento.
     */
    public function add($pessoa)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('solicitacoes/edit/' . $pessoa);
        }

        $subject = trim((string) $this->request->getPost('subject'));

        if ($subject === '') {
            return redirect()->back()->with('error', 'Informe o texto do comentário.');
        }

        $agora = (string) time();

        $this->comentarios->insert([
            'nid'     => (int) $this->request->getPost('nid'),
            'uid'     => (int) session()->get('user_id'),
            'subject' => $subject,
            'created' => $agora,
            'changed' => $agora,
            'name'    => (string) session()->get('username'),
        ]);

        return redirect()->to('solicitacoes/edit/' . $pessoa)
            ->with('message', 'Comentário adicionado com sucesso.');
    }

    /**
     * Edita um comentário (apenas o autor). Atualiza `subject` e `changed`.
     */
    public function update($cid)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('coments');
        }

        $comentario = $this->comentarios->find($cid);

        if ($comentario === null) {
            return redirect()->to('coments')->with('error', 'Comentário não encontrado.');
        }

        if ((int) $comentario['uid'] !== (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'Apenas o autor pode editar este comentário.');
        }

        $subject = trim((string) $this->request->getPost('subject'));

        if ($subject === '') {
            return redirect()->back()->with('error', 'Informe o texto do comentário.');
        }

        $this->comentarios->update((int) $cid, [
            'subject' => $subject,
            'changed' => (string) time(),
        ]);

        $solicitacao = $this->solicitacoes->find((int) $comentario['nid']);

        if ($solicitacao === null) {
            return redirect()->to('coments')->with('message', 'Comentário atualizado com sucesso.');
        }

        return redirect()->to('solicitacoes/edit/' . $solicitacao['spessoa'])
            ->with('message', 'Comentário atualizado com sucesso.');
    }
}
