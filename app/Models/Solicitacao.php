<?php

namespace App\Models;

/**
 * Model da tabela `solicitacoes`.
 */
class Solicitacao extends BaseModel
{
    protected $table         = 'solicitacoes';
    protected $primaryKey    = 'sid';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'spessoa',
        'sagente',
        'obs',
        'pedido',
        'procedimento',
        'responsavel',
        'situacao',
        'sol_sit_id',
        'created',
        'criado',
    ];
}
