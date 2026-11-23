<?php

namespace App\Controllers;

use App\Controllers\Concerns\ManipulaPessoa;
use App\Models\Cidade;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Telefone;
use App\Models\Usuario;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Pessoas — listagem e edição de pessoas (Sprint 5).
 */
class Pessoas extends BaseController
{
    use ManipulaPessoa;

    protected Pessoa $pessoas;

    protected Telefone $telefones;

    protected Usuario $usuarios;

    protected Cidade $cidades;

    protected Procedimento $procedimentos;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url', 'funcoes']);

        $this->pessoas       = new Pessoa();
        $this->telefones     = new Telefone();
        $this->usuarios      = new Usuario();
        $this->cidades       = new Cidade();
        $this->procedimentos = new Procedimento();
    }

    public function index()
    {
        $chave = 'filtros_pessoas';

        if ($this->request->getMethod() === 'POST') {
            session()->set($chave, $this->lerFiltrosDoPost());

            return redirect()->to('pessoas');
        }

        $filtros = session()->get($chave);
        if (! is_array($filtros)) {
            $filtros = [];
        }

        // Busca rápida do navbar (GET ?pes_nome=...).
        $busca = $this->request->getGet('pes_nome');
        if ($busca !== null) {
            $filtros['pessoa'] = trim((string) $busca);
            session()->set($chave, $filtros);
        }

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 20;

        $total   = $this->pessoas->contar($filtros);
        $pessoas = $this->pessoas->consultar($filtros, $perPage, ($page - 1) * $perPage);

        $links = service('pager')->makeLinks($page, $perPage, $total, 'default_full');

        return view('pessoas/index', [
            'title'    => 'Pessoas',
            'pessoas'  => $pessoas,
            'filtros'  => $filtros,
            'clientes' => $this->usuarios->doGrupo(4),
            'links'    => $links,
            'total'    => $total,
        ]);
    }

    public function limpar()
    {
        session()->remove('filtros_pessoas');

        return redirect()->to('pessoas');
    }

    public function edit($id)
    {
        $pessoa = $this->pessoas->find($id);

        if ($pessoa === null) {
            return redirect()->to('pessoas')->with('error', 'Pessoa não encontrada.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (! $this->validate($this->regrasPessoa())) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $pessoaDados = $this->normalizarPessoa([
                'pes_nome'    => trim((string) $this->request->getPost('pes_nome')),
                'pes_cidade'  => trim((string) $this->request->getPost('pes_cidade')),
                'endereco'    => trim((string) $this->request->getPost('endereco')),
                'cep'         => trim((string) $this->request->getPost('cep')),
                'nascimento'  => trim((string) $this->request->getPost('nascimento')),
                'rg'          => trim((string) $this->request->getPost('rg')),
                'cpf'         => trim((string) $this->request->getPost('cpf')),
                'sus'         => trim((string) $this->request->getPost('sus')),
                'mae'         => trim((string) $this->request->getPost('mae')),
                'email'       => trim((string) $this->request->getPost('email')),
                'sexo'        => trim((string) $this->request->getPost('sexo')),
                'pes_cliente' => trim((string) $this->request->getPost('pes_cliente')),
            ], false);

            $this->pessoas->update((int) $id, $pessoaDados);
            $this->telefones->salvarDaPessoa(
                (int) $id,
                trim((string) $this->request->getPost('field_telefones_value'))
            );

            return redirect()->to('solicitacoes/edit/' . $id)
                ->with('message', 'Cadastro atualizado com sucesso.');
        }

        $dados             = $this->dadosFormularioPessoa();
        $dados['title']    = 'Editar pessoa';
        $dados['pessoa']   = $pessoa;
        $dados['telefone'] = $this->telefones->daPessoa((int) $id);

        return view('pessoas/edit', $dados);
    }
}
