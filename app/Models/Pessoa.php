<?php

namespace App\Models;

/**
 * Model da tabela `pessoas`.
 */
class Pessoa extends BaseModel
{
    protected $table         = 'pessoas';
    protected $primaryKey    = 'pes_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'pes_nome',
        'pes_cliente',
        'pes_cidade',
        'sus',
        'cpf',
        'nascimento',
        'endereco',
        'mae',
        'rg',
        'email',
        'uc',
        'sexo',
        'pes_user',
        'pes_pub',
        'pes_catpessoa',
        'cep',
    ];
}
