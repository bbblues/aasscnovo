<?php

namespace App\Models;

/**
 * Model da tabela `situacoes`.
 */
class Situacao extends BaseModel
{
    protected $table         = 'situacoes';
    protected $primaryKey    = 'sit_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'sit_nome',
        'sit_icon',
        'sit_color',
    ];
}
