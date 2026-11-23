<?php

namespace App\Models;

/**
 * Model da tabela `users` (Ion Auth).
 */
class Usuario extends BaseModel
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'ip_address',
        'username',
        'password',
        'salt',
        'email',
        'activation_code',
        'forgotten_password_code',
        'forgotten_password_time',
        'remember_code',
        'created_on',
        'last_login',
        'active',
        'first_name',
        'last_name',
        'company',
        'phone',
    ];

    /**
     * Busca um usuário pelo e-mail (identity do login).
     */
    public function buscarPorEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Usuários ativos de um grupo (usado nos selects de cliente/agente).
     *
     * O legado considera o usuário ativo quando `active` é NULL ou 1.
     *
     * @return list<array<string, mixed>>
     */
    public function doGrupo(int $groupId): array
    {
        return $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.first_name, u.last_name')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->where('ug.group_id', $groupId)
            ->groupStart()
                ->where('u.active', 1)
                ->orWhere('u.active IS NULL')
            ->groupEnd()
            ->orderBy('u.username', 'ASC')
            ->get()
            ->getResultArray();
    }
}
