<?php

namespace App\Models;

/**
 * Model da tabela `catpessoa`.
 *
 * Observação: `cp_id` não possui AUTO_INCREMENT no dump, então ele precisa
 * ser informado explicitamente na inserção.
 */
class CategoriaPessoa extends BaseModel
{
    protected $table         = 'catpessoa';
    protected $primaryKey    = 'cp_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'cp_id',
        'cp_nome',
    ];
}
