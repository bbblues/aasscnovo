<?php

namespace App\Controllers;

use App\Models\CategoriaPessoa;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * CRUD auxiliar de categorias de pessoa (catpessoas) — Sprint 6.
 *
 * `cp_id` não possui AUTO_INCREMENT, então o id é calculado via
 * `CategoriaPessoa::proximoId()`.
 */
class Catpessoas extends BaseController
{
    protected CategoriaPessoa $categorias;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);

        $this->categorias = new CategoriaPessoa();
    }

    public function index()
    {
        return view('auxiliares/index', [
            'title'     => 'Categorias de Pessoa',
            'titulo'    => 'Categorias de Pessoa',
            'coluna'    => 'cp_nome',
            'id'        => 'cp_id',
            'rotaBase'  => 'catpessoas',
            'registros' => $this->categorias->orderBy('cp_nome', 'ASC')->findAll(),
        ]);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('cp_nome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome da categoria.');
            }

            $this->categorias->insert([
                'cp_id'   => $this->categorias->proximoId(),
                'cp_nome' => $nome,
            ]);

            return redirect()->to('catpessoas')->with('message', 'Categoria cadastrada com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Nova categoria',
            'tituloSingular' => 'Categoria',
            'coluna'         => 'cp_nome',
            'rotaBase'       => 'catpessoas',
            'action'         => 'catpessoas/add',
            'registro'       => null,
        ]);
    }

    public function edit($cpId)
    {
        $categoria = $this->categorias->find($cpId);

        if ($categoria === null) {
            return redirect()->to('catpessoas')->with('error', 'Categoria não encontrada.');
        }

        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('cp_nome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome da categoria.');
            }

            $this->categorias->update((int) $cpId, ['cp_nome' => $nome]);

            return redirect()->to('catpessoas')->with('message', 'Categoria atualizada com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Editar categoria',
            'tituloSingular' => 'Categoria',
            'coluna'         => 'cp_nome',
            'rotaBase'       => 'catpessoas',
            'action'         => 'catpessoas/edit/' . $cpId,
            'registro'       => $categoria,
        ]);
    }

    public function remove($cpId)
    {
        $this->categorias->delete((int) $cpId);

        return redirect()->to('catpessoas')->with('message', 'Categoria removida.');
    }
}
