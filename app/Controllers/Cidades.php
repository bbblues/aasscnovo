<?php

namespace App\Controllers;

use App\Models\Cidade;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * CRUD auxiliar de cidades (Sprint 6).
 */
class Cidades extends BaseController
{
    protected Cidade $cidades;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);

        $this->cidades = new Cidade();
    }

    public function index()
    {
        return view('auxiliares/index', [
            'title'     => 'Cidades',
            'titulo'    => 'Cidades',
            'coluna'    => 'cinome',
            'id'        => 'cid',
            'rotaBase'  => 'cidades',
            'registros' => $this->cidades->orderBy('cinome', 'ASC')->findAll(),
        ]);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('cinome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome da cidade.');
            }

            $this->cidades->insert(['cinome' => $nome]);

            return redirect()->to('cidades')->with('message', 'Cidade cadastrada com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Nova cidade',
            'tituloSingular' => 'Cidade',
            'coluna'         => 'cinome',
            'rotaBase'       => 'cidades',
            'action'         => 'cidades/add',
            'registro'       => null,
        ]);
    }

    public function edit($cid)
    {
        $cidade = $this->cidades->find($cid);

        if ($cidade === null) {
            return redirect()->to('cidades')->with('error', 'Cidade não encontrada.');
        }

        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('cinome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome da cidade.');
            }

            $this->cidades->update((int) $cid, ['cinome' => $nome]);

            return redirect()->to('cidades')->with('message', 'Cidade atualizada com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Editar cidade',
            'tituloSingular' => 'Cidade',
            'coluna'         => 'cinome',
            'rotaBase'       => 'cidades',
            'action'         => 'cidades/edit/' . $cid,
            'registro'       => $cidade,
        ]);
    }

    public function remove($cid)
    {
        $this->cidades->delete((int) $cid);

        return redirect()->to('cidades')->with('message', 'Cidade removida.');
    }
}
