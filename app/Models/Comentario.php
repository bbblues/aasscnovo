<?php

namespace App\Models;

/**
 * Model da tabela `comment` (tabela Drupal reaproveitada para
 * comentários/atualizações das solicitações).
 */
class Comentario extends BaseModel
{
    protected $table         = 'comment';
    protected $primaryKey    = 'cid';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'nid',
        'uid',
        'subject',
        'created',
        'changed',
        'name',
    ];
}
