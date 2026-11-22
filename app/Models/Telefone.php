<?php

namespace App\Models;

/**
 * Model da tabela `telefones`.
 *
 * Observação: a tabela não possui chave primária declarada no dump
 * (apenas o índice `idx_entity_id`). `entity_id` é usado como chave lógica.
 */
class Telefone extends BaseModel
{
    protected $table         = 'telefones';
    protected $primaryKey    = 'entity_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'entity_id',
        'field_telefones_value',
    ];
}
