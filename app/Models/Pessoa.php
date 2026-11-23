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

    protected function initialize(): void
    {
        parent::initialize();

        helper('funcoes');
    }

    /**
     * Listagem de pessoas com filtros (responsável, cliente, pessoa, município
     * e período) persistidos em sessão no controller.
     *
     * @param array<string, string> $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function consultar(array $filtros, int $limit, int $offset): array
    {
        $builder = $this->db->table('pessoas p')
            ->select('p.pes_id, p.pes_nome, p.pes_pub, p.pes_cidade, p.email, p.pes_cliente, u.username AS cliente, t.field_telefones_value AS telefone')
            ->join('telefones t', 't.entity_id = p.pes_id', 'left')
            ->join('users u', 'u.id = p.pes_cliente', 'left')
            ->join('users uresp', 'uresp.id = p.pes_user', 'left')
            ->groupBy('p.pes_id')
            ->orderBy('TRIM(p.pes_nome)', 'ASC');

        $this->aplicarFiltros($builder, $filtros);

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Total de pessoas para a paginação (mesmos filtros da consulta).
     *
     * @param array<string, string> $filtros
     */
    public function contar(array $filtros): int
    {
        $builder = $this->db->table('pessoas p')
            ->join('telefones t', 't.entity_id = p.pes_id', 'left')
            ->join('users u', 'u.id = p.pes_cliente', 'left')
            ->join('users uresp', 'uresp.id = p.pes_user', 'left');

        $this->aplicarFiltros($builder, $filtros);

        $row = $builder->select('COUNT(DISTINCT p.pes_id) AS total')->get()->getRow();

        return (int) ($row->total ?? 0);
    }

    /**
     * @param \CodeIgniter\Database\BaseBuilder $builder
     * @param array<string, string>             $filtros
     */
    private function aplicarFiltros($builder, array $filtros): void
    {
        if (! empty($filtros['pessoa'])) {
            $builder->like('p.pes_nome', $filtros['pessoa']);
        }

        if (! empty($filtros['municipio'])) {
            $builder->like('p.pes_cidade', $filtros['municipio']);
        }

        if (! empty($filtros['cliente'])) {
            $builder->where('p.pes_cliente', (string) $filtros['cliente']);
        }

        if (! empty($filtros['responsavel'])) {
            $builder->groupStart()
                ->like('uresp.username', $filtros['responsavel'])
                ->orLike("CONCAT(uresp.first_name, ' ', uresp.last_name)", $filtros['responsavel'])
                ->groupEnd();
        }

        if (! empty($filtros['data_inicio'])) {
            $builder->where('p.pes_pub >=', $filtros['data_inicio']);
        }

        if (! empty($filtros['data_fim'])) {
            $builder->where('p.pes_pub <=', data_final($filtros['data_fim']));
        }
    }
}
