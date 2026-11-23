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

    /**
     * Retorna o telefone (primeiro registro) de uma pessoa.
     */
    public function daPessoa(int $entityId): ?string
    {
        $row = $this->where('entity_id', $entityId)->first();

        return $row['field_telefones_value'] ?? null;
    }

    /**
     * Substitui o(s) telefone(s) da pessoa pelo valor informado.
     *
     * A tabela `telefones` não possui chave primária, então a forma mais
     * simples e segura é remover os registros existentes e inserir um novo
     * (apenas quando houver valor).
     */
    public function salvarDaPessoa(int $entityId, string $telefone): void
    {
        $this->where('entity_id', $entityId)->delete();

        if (trim($telefone) !== '') {
            $this->insert([
                'entity_id'             => $entityId,
                'field_telefones_value' => trim($telefone),
            ]);
        }
    }
}
