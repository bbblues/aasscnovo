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
}
