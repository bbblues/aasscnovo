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

    protected function initialize(): void
    {
        parent::initialize();

        helper('funcoes');
    }

    /**
     * Listagem de comentários com filtros (idênticos aos de pessoas/solicitações).
     *
     * @param array<string, string> $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function consultar(array $filtros, int $limit, int $offset): array
    {
        $builder = $this->builderBase();

        $this->aplicarFiltros($builder, $filtros);

        return $builder
            ->orderBy('c.changed', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Total de comentários para a paginação (mesmos filtros da consulta).
     *
     * @param array<string, string> $filtros
     */
    public function contar(array $filtros): int
    {
        $builder = $this->builderBase();

        $this->aplicarFiltros($builder, $filtros);

        $row = $builder->select('COUNT(*) AS total')->get()->getRow();

        return (int) ($row->total ?? 0);
    }

    /**
     * Comentários de uma solicitação (accordion da tela de atendimento),
     * do mais antigo para o mais recente.
     *
     * @return list<array<string, mixed>>
     */
    public function daSolicitacao(int $sid): array
    {
        return $this->db->table('comment c')
            ->select('c.*, u.username AS autor_username')
            ->join('users u', 'u.id = c.uid', 'left')
            ->where('c.nid', $sid)
            ->orderBy('c.created', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Comentários para o relatório de atividades (sem paginação).
     *
     * @param array<string, string> $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function ultimasAtualizacoes(array $filtros): array
    {
        $builder = $this->db->table('comment c')
            ->select('c.subject AS atividade, c.created, p.pes_nome AS nome, s.pedido')
            ->join('solicitacoes s', 's.sid = c.nid')
            ->join('pessoas p', 'p.pes_id = s.spessoa');

        $this->aplicarFiltros($builder, $filtros);

        return $builder->get()->getResultArray();
    }

    /**
     * Base comum das consultas de comentários (joins + colunas).
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function builderBase()
    {
        return $this->db->table('comment c')
            ->select('c.cid, c.nid, c.uid, c.name, c.subject, c.created, c.changed, p.pes_id, p.pes_nome, s.pedido')
            ->join('solicitacoes s', 's.sid = c.nid')
            ->join('pessoas p', 'p.pes_id = s.spessoa');
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
            $builder->like('s.responsavel', $filtros['responsavel']);
        }

        // Em comentários o período é aplicado sobre `changed` (timestamp),
        // com `data_final()` somando +1 dia no fim (docs/02).
        if (! empty($filtros['data_inicio'])) {
            $builder->where('c.changed >=', strtotime($filtros['data_inicio']));
        }

        if (! empty($filtros['data_fim'])) {
            $builder->where('c.changed <=', strtotime(data_final($filtros['data_fim'])));
        }
    }
}
