<?php

namespace App\Models;

/**
 * Model da tabela `procedimentos`.
 */
class Procedimento extends BaseModel
{
    protected $table         = 'procedimentos';
    protected $primaryKey    = 'med_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'med_nome',
    ];
}
