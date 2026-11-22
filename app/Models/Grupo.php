<?php

namespace App\Models;

/**
 * Model da tabela `groups` (Ion Auth).
 *
 * Observação: `id` não possui AUTO_INCREMENT no dump, então ele precisa ser
 * informado explicitamente na inserção.
 */
class Grupo extends BaseModel
{
    protected $table         = 'groups';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id',
        'name',
        'description',
    ];
}
