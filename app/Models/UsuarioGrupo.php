<?php

namespace App\Models;

/**
 * Model da tabela `users_groups` (Ion Auth).
 */
class UsuarioGrupo extends BaseModel
{
    protected $table         = 'users_groups';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id',
        'group_id',
    ];

    /**
     * Retorna os grupos de um usuário (id + nome), via join com `groups`.
     *
     * @return list<array{group_id: int, name: string}>
     */
    public function gruposDoUsuario(int $userId): array
    {
        return $this->db->table('users_groups ug')
            ->select('ug.group_id, g.name')
            ->join('groups g', 'g.id = ug.group_id')
            ->where('ug.user_id', $userId)
            ->orderBy('ug.group_id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
