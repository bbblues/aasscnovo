<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;
use App\Models\Grupo;
use App\Models\Usuario;
use App\Models\UsuarioGrupo;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Controller de autenticação e gestão de usuários/grupos (Sprint 3).
 *
 * Rotas públicas: login, logout, esqueci/reset de senha (protegidas apenas
 * pelo filtro `auth` + CSRF). Rotas de gestão exigem o filtro `group:admin`.
 */
class Auth extends BaseController
{
    protected Autenticacao $autenticacao;

    protected Usuario $usuarios;

    protected Grupo $grupos;

    protected UsuarioGrupo $usuariosGrupos;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);

        $this->autenticacao   = new Autenticacao();
        $this->usuarios       = new Usuario();
        $this->grupos         = new Grupo();
        $this->usuariosGrupos = new UsuarioGrupo();
    }

    public function login()
    {
        if ($this->autenticacao->estaLogado()) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'POST') {
            $identity = trim((string) $this->request->getPost('identity'));
            $password = (string) $this->request->getPost('password');

            if ($identity === '' || $password === '') {
                return redirect()->back()->withInput()->with('error', 'Informe e-mail e senha.');
            }

            if ($this->autenticacao->bloqueado($identity)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.');
            }

            if ($this->autenticacao->tentarLogin($identity, $password)) {
                $this->autenticacao->limparTentativas($identity);

                return redirect()->to('/')->with('message', 'Login realizado com sucesso.');
            }

            $this->autenticacao->registrarTentativa($identity, $this->request->getIPAddress());

            return redirect()->back()->withInput()
                ->with('error', 'E-mail ou senha inválidos.');
        }

        return view('auth/login');
    }

    public function logout()
    {
        $this->autenticacao->logout();

        return redirect()->to('auth/login')->with('message', 'Sessão encerrada.');
    }

    public function changePassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'old'         => 'required',
                'new'         => 'required|min_length[8]',
                'new_confirm' => 'required|matches[new]',
            ];
            $messages = [
                'old'         => ['required' => 'Informe a senha atual.'],
                'new'         => [
                    'required'   => 'Informe a nova senha.',
                    'min_length' => 'A nova senha deve ter no mínimo 8 caracteres.',
                ],
                'new_confirm' => [
                    'required' => 'Confirme a nova senha.',
                    'matches'  => 'A confirmação não confere com a nova senha.',
                ],
            ];

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $userId = (int) session()->get('user_id');
            $atual  = (string) $this->request->getPost('old');
            $nova   = (string) $this->request->getPost('new');

            if (! $this->autenticacao->trocarSenha($userId, $atual, $nova)) {
                return redirect()->back()->with('error', 'Senha atual incorreta.');
            }

            return redirect()->to('/')->with('message', 'Senha alterada com sucesso.');
        }

        return view('auth/change_password');
    }

    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = trim((string) $this->request->getPost('email'));

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('error', 'Informe um e-mail válido.');
            }

            $token = $this->autenticacao->recuperarSenha($email);

            // Mensagem genérica para não revelar se o e-mail existe.
            $mensagem = 'Se o e-mail informado existir, as instruções para redefinir a senha foram enviadas.';

            if ($token !== null) {
                $link = site_url('auth/reset_password/' . $token);

                if (config('Email')->fromEmail !== '') {
                    $this->enviarEmailRecuperacao($email, $link);
                } else {
                    // Sem SMTP configurado (dev): exibe o link na tela.
                    $mensagem = 'E-mail não configurado. Use este link para redefinir a senha (dev): ' . $link;
                }
            }

            return redirect()->to('auth/forgot_password')->with('message', $mensagem);
        }

        return view('auth/forgot_password');
    }

    public function resetPassword($code)
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password'         => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
            ];
            $messages = [
                'password'         => [
                    'required'   => 'Informe a nova senha.',
                    'min_length' => 'A nova senha deve ter no mínimo 8 caracteres.',
                ],
                'password_confirm' => [
                    'required' => 'Confirme a nova senha.',
                    'matches'  => 'A confirmação não confere com a nova senha.',
                ],
            ];

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $nova = (string) $this->request->getPost('password');

            if (! $this->autenticacao->resetarSenha((string) $code, $nova)) {
                return redirect()->back()->with('error', 'Link inválido ou expirado.');
            }

            return redirect()->to('auth/login')->with('message', 'Senha redefinida com sucesso. Faça login.');
        }

        return view('auth/reset_password', ['code' => (string) $code]);
    }

    public function index()
    {
        $usuarios = $this->usuarios->orderBy('id', 'ASC')->findAll();

        foreach ($usuarios as &$usuario) {
            $usuario['grupos'] = $this->usuariosGrupos->gruposDoUsuario((int) $usuario['id']);
        }
        unset($usuario);

        return view('auth/index', [
            'usuarios' => $usuarios,
            'grupos'   => $this->grupos->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function createUser()
    {
        $grupos = $this->grupos->orderBy('id', 'ASC')->findAll();

        if ($this->request->getMethod() === 'POST') {
            [$rules, $messages] = $this->regrasUsuario(false);

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $post   = $this->request->getPost();
            $userId = $this->usuarios->insert([
                'ip_address' => $this->request->getIPAddress(),
                'username'   => $post['username'] ?: null,
                'password'   => password_hash((string) $post['password'], PASSWORD_BCRYPT),
                'email'      => $post['email'],
                'created_on' => time(),
                'last_login' => null,
                'active'     => 1,
                'first_name' => $post['first_name'] ?: null,
                'last_name'  => $post['last_name'] ?: null,
                'company'    => $post['company'] ?: null,
                'phone'      => $post['phone'] ?: null,
            ]);

            $this->salvarGrupos((int) $userId, (array) $this->request->getPost('groups'));

            return redirect()->to('auth')->with('message', 'Usuário criado com sucesso.');
        }

        return view('auth/user_form', [
            'user'               => null,
            'grupos'             => $grupos,
            'gruposSelecionados' => [],
        ]);
    }

    public function editUser($id)
    {
        $user = $this->usuarios->find($id);

        if ($user === null) {
            return redirect()->to('auth')->with('error', 'Usuário não encontrado.');
        }

        $grupos             = $this->grupos->orderBy('id', 'ASC')->findAll();
        $gruposSelecionados = array_column($this->usuariosGrupos->gruposDoUsuario((int) $id), 'group_id');

        if ($this->request->getMethod() === 'POST') {
            [$rules, $messages] = $this->regrasUsuario(true, (int) $id);

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $post  = $this->request->getPost();
            $dados = [
                'username'   => $post['username'] ?: null,
                'email'      => $post['email'],
                'first_name' => $post['first_name'] ?: null,
                'last_name'  => $post['last_name'] ?: null,
                'company'    => $post['company'] ?: null,
                'phone'      => $post['phone'] ?: null,
            ];

            if (! empty($post['password'])) {
                $dados['password'] = password_hash((string) $post['password'], PASSWORD_BCRYPT);
            }

            $this->usuarios->update((int) $id, $dados);
            $this->salvarGrupos((int) $id, (array) $this->request->getPost('groups'));

            return redirect()->to('auth')->with('message', 'Usuário atualizado com sucesso.');
        }

        return view('auth/user_form', [
            'user'               => $user,
            'grupos'             => $grupos,
            'gruposSelecionados' => $gruposSelecionados,
        ]);
    }

    public function deactivate($id)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('auth');
        }

        if ((int) $id === (int) session()->get('user_id')) {
            return redirect()->to('auth')->with('error', 'Você não pode desativar a própria conta.');
        }

        $this->usuarios->update((int) $id, ['active' => 0]);

        return redirect()->to('auth')->with('message', 'Usuário desativado.');
    }

    public function activate($id)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('auth');
        }

        $this->usuarios->update((int) $id, ['active' => 1, 'activation_code' => null]);

        return redirect()->to('auth')->with('message', 'Usuário ativado.');
    }

    public function createGroup()
    {
        if ($this->request->getMethod() === 'POST') {
            [$rules, $messages] = $this->regrasGrupo(false);

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $post = $this->request->getPost();

            $this->grupos->insert([
                'id'          => $this->autenticacao->proximoIdGrupo(),
                'name'        => $post['name'],
                'description' => $post['description'] ?? '',
            ]);

            return redirect()->to('auth')->with('message', 'Grupo criado com sucesso.');
        }

        return view('auth/group_form', ['group' => null]);
    }

    public function editGroup($id)
    {
        $group = $this->grupos->find($id);

        if ($group === null) {
            return redirect()->to('auth')->with('error', 'Grupo não encontrado.');
        }

        if ($this->request->getMethod() === 'POST') {
            [$rules, $messages] = $this->regrasGrupo(true, (int) $id);

            if (! $this->validate($rules, $messages)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $post = $this->request->getPost();

            $this->grupos->update((int) $id, [
                'name'        => $post['name'],
                'description' => $post['description'] ?? '',
            ]);

            return redirect()->to('auth')->with('message', 'Grupo atualizado com sucesso.');
        }

        return view('auth/group_form', ['group' => $group]);
    }

    /**
     * Substitui os grupos do usuário pelos selecionados no formulário.
     *
     * @param list<int> $groupIds
     */
    private function salvarGrupos(int $userId, array $groupIds): void
    {
        $this->usuariosGrupos->where('user_id', $userId)->delete();

        foreach ($groupIds as $groupId) {
            $this->usuariosGrupos->insert([
                'user_id'  => $userId,
                'group_id' => (int) $groupId,
            ]);
        }
    }

    /**
     * @return array{0: array<string, string>, 1: array<string, array<string, string>>}
     */
    private function regrasUsuario(bool $editando, int $id = null): array
    {
        $regraEmail = 'required|valid_email|max_length[100]';
        $regraEmail .= $editando ? '|is_unique[users.email,id,' . $id . ']' : '|is_unique[users.email]';

        $rules = [
            'email'            => $regraEmail,
            'username'         => 'permit_empty|max_length[100]',
            'first_name'       => 'permit_empty|max_length[50]',
            'last_name'        => 'permit_empty|max_length[50]',
            'company'          => 'permit_empty|max_length[100]',
            'phone'            => 'permit_empty|max_length[20]',
            'password'         => $editando ? 'permit_empty|min_length[8]' : 'required|min_length[8]',
            'password_confirm' => $editando ? 'permit_empty|matches[password]' : 'required|matches[password]',
        ];

        $messages = [
            'email' => [
                'required'    => 'Informe o e-mail.',
                'valid_email' => 'Informe um e-mail válido.',
                'is_unique'   => 'Este e-mail já está em uso.',
                'max_length'  => 'O e-mail deve ter no máximo 100 caracteres.',
            ],
            'username'         => ['max_length' => 'O nome de usuário deve ter no máximo 100 caracteres.'],
            'first_name'       => ['max_length' => 'O nome deve ter no máximo 50 caracteres.'],
            'last_name'        => ['max_length' => 'O sobrenome deve ter no máximo 50 caracteres.'],
            'company'          => ['max_length' => 'A empresa deve ter no máximo 100 caracteres.'],
            'phone'            => ['max_length' => 'O telefone deve ter no máximo 20 caracteres.'],
            'password'         => [
                'required'   => 'Informe a senha.',
                'min_length' => 'A senha deve ter no mínimo 8 caracteres.',
            ],
            'password_confirm' => [
                'required' => 'Confirme a senha.',
                'matches'  => 'A confirmação não confere com a senha.',
            ],
        ];

        return [$rules, $messages];
    }

    /**
     * @return array{0: array<string, string>, 1: array<string, array<string, string>>}
     */
    private function regrasGrupo(bool $editando, int $id = null): array
    {
        $regraNome = 'required|max_length[20]';
        $regraNome .= $editando ? '|is_unique[groups.name,id,' . $id . ']' : '|is_unique[groups.name]';

        $rules = [
            'name'        => $regraNome,
            'description' => 'permit_empty|max_length[100]',
        ];

        $messages = [
            'name'        => [
                'required'   => 'Informe o nome do grupo.',
                'max_length' => 'O nome do grupo deve ter no máximo 20 caracteres.',
                'is_unique'  => 'Já existe um grupo com este nome.',
            ],
            'description' => ['max_length' => 'A descrição deve ter no máximo 100 caracteres.'],
        ];

        return [$rules, $messages];
    }

    private function enviarEmailRecuperacao(string $email, string $link): void
    {
        $emailService = service('email');
        $emailService->setTo($email);
        $emailService->setSubject('Redefinição de senha — Sistema AASSC');
        $emailService->setMessage("Acesse o link a seguir para redefinir sua senha:\n\n" . $link);
        $emailService->send();
    }
}
