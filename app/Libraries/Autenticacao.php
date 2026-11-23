<?php

namespace App\Libraries;

use App\Models\Usuario;
use App\Models\UsuarioGrupo;
use Config\Database;

/**
 * Autenticacao — serviço de autenticação e autorização (Sprint 3).
 *
 * Substitui o Ion Auth usando as tabelas legadas (`users`, `groups`,
 * `users_groups`, `login_attempts`). Mantém compatibilidade com as senhas
 * bcrypt já existentes (`password_verify`/`password_hash`).
 */
class Autenticacao
{
    /** Limite de tentativas de login falhas antes do bloqueio temporário. */
    public const TENTATIVAS_MAXIMAS = 5;

    /** Janela (em minutos) considerada no rate limiting. */
    public const JANELA_MINUTOS = 15;

    /** Validade do token de recuperação de senha (1 hora). */
    public const EXPIRACAO_RESET = 3600;

    protected Usuario $usuarios;

    protected UsuarioGrupo $usuariosGrupos;

    protected $db;

    public function __construct()
    {
        $this->usuarios       = new Usuario();
        $this->usuariosGrupos = new UsuarioGrupo();
        $this->db             = Database::connect();
    }

    public function estaLogado(): bool
    {
        return (bool) session()->get('logged_in');
    }

    /**
     * Valida as credenciais e, em caso de sucesso, inicia a sessão.
     *
     * @return array<string, mixed>|false
     */
    public function tentarLogin(string $identity, string $password): array|false
    {
        $usuario = $this->usuarios->buscarPorEmail($identity);

        if ($usuario === null) {
            return false;
        }

        if (! $this->estaAtivo($usuario)) {
            return false;
        }

        if (! password_verify($password, $usuario['password'])) {
            return false;
        }

        $grupos = $this->gruposDoUsuario((int) $usuario['id']);

        session()->set([
            'logged_in'  => true,
            'user_id'    => (int) $usuario['id'],
            'username'   => $usuario['username'],
            'email'      => $usuario['email'],
            'first_name' => $usuario['first_name'],
            'last_name'  => $usuario['last_name'],
            'active'     => $this->estaAtivo($usuario),
            'groups'     => $grupos['nomes'],
            'group_ids'  => $grupos['ids'],
        ]);

        $this->usuarios->update((int) $usuario['id'], ['last_login' => time()]);

        return $usuario;
    }

    public function logout(): void
    {
        session()->destroy();
    }

    /**
     * Troca a senha do usuário logado, validando a senha atual.
     */
    public function trocarSenha(int $userId, string $senhaAtual, string $senhaNova): bool
    {
        $usuario = $this->usuarios->find($userId);

        if ($usuario === null) {
            return false;
        }

        if (! password_verify($senhaAtual, $usuario['password'])) {
            return false;
        }

        return (bool) $this->usuarios->update($userId, [
            'password' => password_hash($senhaNova, PASSWORD_BCRYPT),
        ]);
    }

    /**
     * Gera e grava o token de recuperação de senha. Retorna o token ou null.
     */
    public function recuperarSenha(string $email): ?string
    {
        $usuario = $this->usuarios->buscarPorEmail($email);

        if ($usuario === null) {
            return null;
        }

        $token = bin2hex(random_bytes(20)); // 40 caracteres (coluna varchar 40)

        $this->usuarios->update((int) $usuario['id'], [
            'forgotten_password_code' => $token,
            'forgotten_password_time' => time(),
        ]);

        return $token;
    }

    /**
     * Redefine a senha a partir de um token válido e não expirado.
     */
    public function resetarSenha(string $codigo, string $senhaNova): bool
    {
        $usuario = $this->usuarios->where('forgotten_password_code', $codigo)->first();

        if ($usuario === null) {
            return false;
        }

        $tempo = (int) ($usuario['forgotten_password_time'] ?? 0);

        if ($tempo === 0 || (time() - $tempo) > self::EXPIRACAO_RESET) {
            return false;
        }

        return (bool) $this->usuarios->update((int) $usuario['id'], [
            'password'                => password_hash($senhaNova, PASSWORD_BCRYPT),
            'forgotten_password_code' => null,
            'forgotten_password_time' => null,
        ]);
    }

    /**
     * Indica se a identidade atingiu o limite de tentativas na janela atual.
     */
    public function bloqueado(string $identity): bool
    {
        $limite = time() - (self::JANELA_MINUTOS * 60);

        $total = $this->db->table('login_attempts')
            ->where('login', $identity)
            ->where('time >=', $limite)
            ->countAllResults();

        return $total >= self::TENTATIVAS_MAXIMAS;
    }

    public function registrarTentativa(string $identity, string $ip): void
    {
        $this->db->table('login_attempts')->insert([
            'id'         => $this->proximoIdLoginAttempts(),
            'ip_address' => substr($ip, 0, 15),
            'login'      => $identity,
            'time'       => time(),
        ]);
    }

    public function limparTentativas(string $identity): void
    {
        $this->db->table('login_attempts')
            ->where('login', $identity)
            ->delete();
    }

    /**
     * @return array{nomes: list<string>, ids: list<int>}
     */
    public function gruposDoUsuario(int $userId): array
    {
        $nomes = [];
        $ids   = [];

        foreach ($this->usuariosGrupos->gruposDoUsuario($userId) as $linha) {
            $ids[]   = (int) $linha['group_id'];
            $nomes[] = $linha['name'];
        }

        return ['nomes' => $nomes, 'ids' => $ids];
    }

    public function proximoIdGrupo(): int
    {
        return $this->proximoId('groups', 'id');
    }

    private function proximoIdLoginAttempts(): int
    {
        return $this->proximoId('login_attempts', 'id');
    }

    /**
     * `groups` e `login_attempts` não possuem AUTO_INCREMENT no dump, então o
     * próximo id é calculado a partir do maior valor atual.
     */
    private function proximoId(string $tabela, string $coluna): int
    {
        $row = $this->db->table($tabela)
            ->selectMax($coluna, 'maximo')
            ->get()
            ->getRow();

        return (int) ($row->maximo ?? 0) + 1;
    }

    /**
     * Legado: `active` pode ser NULL. Considera ativo quando NULL ou 1.
     */
    private function estaAtivo(array $usuario): bool
    {
        return $usuario['active'] === null || (int) $usuario['active'] === 1;
    }
}
