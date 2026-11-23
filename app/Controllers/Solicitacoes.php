<?php

namespace App\Controllers;

use App\Controllers\Concerns\ManipulaPessoa;
use App\Models\Cidade;
use App\Models\Pessoa;
use App\Models\Procedimento;
use App\Models\Solicitacao;
use App\Models\Telefone;
use App\Models\Usuario;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Solicitações — listagem, atendimento e cadastro (Sprint 5).
 */
class Solicitacoes extends BaseController
{
    use ManipulaPessoa;

    protected Pessoa $pessoas;

    protected Solicitacao $solicitacoes;

    protected Telefone $telefones;

    protected Usuario $usuarios;

    protected Cidade $cidades;

    protected Procedimento $procedimentos;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url', 'funcoes']);

        $this->pessoas       = new Pessoa();
        $this->solicitacoes  = new Solicitacao();
        $this->telefones     = new Telefone();
        $this->usuarios      = new Usuario();
        $this->cidades       = new Cidade();
        $this->procedimentos = new Procedimento();
    }

    public function index()
    {
        return $this->listar(false);
    }

    public function abertas()
    {
        return $this->listar(true);
    }

    public function limpar()
    {
        session()->remove('filtros_solicitacoes');

        return redirect()->to('solicitacoes');
    }

    /**
     * Tela de atendimento: cadastro da pessoa (esquerda) + accordion de
     * solicitações (direita).
     */
    public function edit($pesId)
    {
        $pessoa = $this->pessoas->find($pesId);

        if ($pessoa === null) {
            return redirect()->to('solicitacoes')->with('error', 'Pessoa não encontrada.');
        }

        $dados                 = $this->dadosFormularioPessoa();
        $dados['title']        = 'Atendimento — ' . $pessoa['pes_nome'];
        $dados['pessoa']       = $pessoa;
        $dados['telefone']     = $this->telefones->daPessoa((int) $pesId);
        $dados['solicitacoes'] = $this->solicitacoes->daPessoa((int) $pesId);

        return view('solicitacoes/edit', $dados);
    }

    /**
     * Atualiza uma solicitação existente (POST /solicitacoes/update/{sid}).
     */
    public function update($sid)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('solicitacoes');
        }

        $solicitacao = $this->solicitacoes->find($sid);

        if ($solicitacao === null) {
            return redirect()->to('solicitacoes')->with('error', 'Solicitação não encontrada.');
        }

        if (! $this->validate($this->regrasSolicitacao())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $solSitId = (int) $this->request->getPost('sol_sit_id');

        $this->solicitacoes->update((int) $sid, [
            'sagente'      => $this->nullableInt($this->request->getPost('sagente')),
            'sol_sit_id'   => $solSitId,
            'situacao'     => situacao_by_id($solSitId),
            'pedido'       => trim((string) $this->request->getPost('pedido')),
            'procedimento' => trim((string) $this->request->getPost('procedimento')),
            'responsavel'  => trim((string) $this->request->getPost('responsavel')),
            'obs'          => trim((string) $this->request->getPost('obs')),
        ]);

        return redirect()->to('solicitacoes/edit/' . $solicitacao['spessoa'])
            ->with('message', 'Solicitação atualizada com sucesso.');
    }

    /**
     * Cadastra uma nova solicitação (POST /solicitacoes/add).
     */
    public function add()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('solicitacoes');
        }

        if (! $this->validate($this->regrasSolicitacao())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $spessoa = (int) $this->request->getPost('spessoa');

        $this->gravarSolicitacao([
            'spessoa'      => $spessoa,
            'sagente'      => $this->nullableInt($this->request->getPost('sagente')),
            'sol_sit_id'   => (int) $this->request->getPost('sol_sit_id'),
            'pedido'       => trim((string) $this->request->getPost('pedido')),
            'procedimento' => trim((string) $this->request->getPost('procedimento')),
            'responsavel'  => trim((string) $this->request->getPost('responsavel')),
            'obs'          => trim((string) $this->request->getPost('obs')),
        ]);

        return redirect()->to('solicitacoes/edit/' . $spessoa)
            ->with('message', 'Solicitação cadastrada com sucesso.');
    }

    /**
     * Cadastra pessoa (+ telefone) e, opcionalmente, uma solicitação.
     */
    public function addPessoa()
    {
        if ($this->request->getMethod() === 'POST') {
            $post     = $this->request->getPost();
            $separado = separa_post(is_array($post) ? $post : []);

            if (! $this->validate($this->regrasPessoa())) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $pessoa = $this->normalizarPessoa($separado['pessoa'], true);

            $pesId = $this->pessoas->insert($pessoa);
            if (! $pesId) {
                return redirect()->back()->withInput()
                    ->with('error', 'Não foi possível cadastrar a pessoa.');
            }

            $telefone = trim((string) ($separado['telefone']['field_telefones_value'] ?? ''));
            $this->telefones->salvarDaPessoa((int) $pesId, $telefone);

            $pedido = trim((string) ($separado['solicitacao']['pedido'] ?? ''));
            if ($pedido !== '') {
                $this->gravarSolicitacao([
                    'spessoa'      => (int) $pesId,
                    'sagente'      => $this->nullableInt($separado['solicitacao']['sagente'] ?? null),
                    'sol_sit_id'   => (int) ($separado['solicitacao']['sol_sit_id'] ?? 1),
                    'pedido'       => $pedido,
                    'procedimento' => trim((string) ($separado['solicitacao']['procedimento'] ?? '')),
                    'responsavel'  => trim((string) ($separado['solicitacao']['responsavel'] ?? '')),
                    'obs'          => trim((string) ($separado['solicitacao']['obs'] ?? '')),
                ]);
            }

            return redirect()->to('solicitacoes/edit/' . $pesId)
                ->with('message', 'Cadastro realizado com sucesso.');
        }

        $dados             = $this->dadosFormularioPessoa();
        $dados['title']    = 'Cadastrar pessoa';
        $dados['pessoa']   = null;
        $dados['telefone'] = null;

        return view('solicitacoes/add_pessoa', $dados);
    }

    private function listar(bool $apenasAbertas)
    {
        $chave = 'filtros_solicitacoes';

        if ($this->request->getMethod() === 'POST') {
            session()->set($chave, $this->lerFiltrosDoPost());

            return redirect()->to($apenasAbertas ? 'solicitacoes/abertas' : 'solicitacoes');
        }

        $filtros = session()->get($chave);
        if (! is_array($filtros)) {
            $filtros = [];
        }

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 20;

        $total        = $this->solicitacoes->contar($filtros, $apenasAbertas);
        $solicitacoes = $this->solicitacoes->consultar($filtros, $perPage, ($page - 1) * $perPage, $apenasAbertas);

        $links = service('pager')->makeLinks($page, $perPage, $total, 'default_full');

        return view('solicitacoes/index', [
            'title'         => $apenasAbertas ? 'Solicitações em Aberto' : 'Solicitações',
            'apenasAbertas' => $apenasAbertas,
            'solicitacoes'  => $solicitacoes,
            'filtros'       => $filtros,
            'clientes'      => $this->usuarios->doGrupo(4),
            'links'         => $links,
            'total'         => $total,
        ]);
    }

    /**
     * Grava uma solicitação aplicando as regras de negócio:
     * `situacao` derivada de `sol_sit_id`, `criado = date('Y-m-d')` e
     * `created = UNIX_TIMESTAMP()`.
     *
     * @param array<string, mixed> $dados
     */
    private function gravarSolicitacao(array $dados): void
    {
        $solSitId = (int) $dados['sol_sit_id'];

        $this->solicitacoes->insert([
            'spessoa'      => (int) $dados['spessoa'],
            'sagente'      => $dados['sagente'],
            'obs'          => $dados['obs'] ?? null,
            'pedido'       => $dados['pedido'],
            'procedimento' => $dados['procedimento'] ?? null,
            'responsavel'  => $dados['responsavel'],
            'situacao'     => situacao_by_id($solSitId),
            'sol_sit_id'   => $solSitId,
            'created'      => (string) time(),
            'criado'       => date('Y-m-d'),
        ]);
    }

    /**
     * Regras de validação dos campos de solicitação.
     *
     * @return array<string, string>
     */
    private function regrasSolicitacao(): array
    {
        return [
            'spessoa'      => 'required|integer',
            'sol_sit_id'   => 'required|in_list[1,2,3]',
            'pedido'       => 'required',
            'responsavel'  => 'required',
            'sagente'      => 'permit_empty|integer',
            'procedimento' => 'permit_empty|max_length[255]',
            'obs'          => 'permit_empty',
        ];
    }
}
