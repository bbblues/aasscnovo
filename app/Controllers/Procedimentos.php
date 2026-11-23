<?php

namespace App\Controllers;

use App\Models\Procedimento;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * CRUD auxiliar de procedimentos (Sprint 6).
 */
class Procedimentos extends BaseController
{
    protected Procedimento $procedimentos;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);

        $this->procedimentos = new Procedimento();
    }

    public function index()
    {
        return view('auxiliares/index', [
            'title'     => 'Procedimentos',
            'titulo'    => 'Procedimentos',
            'coluna'    => 'med_nome',
            'id'        => 'med_id',
            'rotaBase'  => 'procedimentos',
            'registros' => $this->procedimentos->orderBy('med_nome', 'ASC')->findAll(),
        ]);
    }

    public function add()
    {
        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('med_nome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome do procedimento.');
            }

            $this->procedimentos->insert(['med_nome' => $nome]);

            return redirect()->to('procedimentos')->with('message', 'Procedimento cadastrado com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Novo procedimento',
            'tituloSingular' => 'Procedimento',
            'coluna'         => 'med_nome',
            'rotaBase'       => 'procedimentos',
            'action'         => 'procedimentos/add',
            'registro'       => null,
        ]);
    }

    public function edit($medId)
    {
        $procedimento = $this->procedimentos->find($medId);

        if ($procedimento === null) {
            return redirect()->to('procedimentos')->with('error', 'Procedimento não encontrado.');
        }

        if ($this->request->getMethod() === 'POST') {
            $nome = trim((string) $this->request->getPost('med_nome'));

            if ($nome === '') {
                return redirect()->back()->withInput()->with('error', 'Informe o nome do procedimento.');
            }

            $this->procedimentos->update((int) $medId, ['med_nome' => $nome]);

            return redirect()->to('procedimentos')->with('message', 'Procedimento atualizado com sucesso.');
        }

        return view('auxiliares/form', [
            'title'          => 'Editar procedimento',
            'tituloSingular' => 'Procedimento',
            'coluna'         => 'med_nome',
            'rotaBase'       => 'procedimentos',
            'action'         => 'procedimentos/edit/' . $medId,
            'registro'       => $procedimento,
        ]);
    }

    public function remove($medId)
    {
        $this->procedimentos->delete((int) $medId);

        return redirect()->to('procedimentos')->with('message', 'Procedimento removido.');
    }
}
