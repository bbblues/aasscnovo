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

    /**
     * `cp_id` não possui AUTO_INCREMENT no dump, então o próximo id é calculado
     * a partir do maior valor atual.
     */
    public function proximoId(): int
    {
        $row = $this->db->table('catpessoa')
            ->selectMax('cp_id', 'maximo')
            ->get()
            ->getRow();

        return (int) ($row->maximo ?? 0) + 1;
    }
}
