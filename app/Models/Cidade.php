<?php

namespace App\Models;

/**
 * Model da tabela `cidades`.
 */
class Cidade extends BaseModel
{
    protected $table         = 'cidades';
    protected $primaryKey    = 'cid';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'cinome',
    ];
}
