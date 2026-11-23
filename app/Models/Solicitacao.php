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

    /**
     * Solicitações em aberto do agente logado (tabela do dashboard).
     *
     * Filtro: `sagente = $userId` e `sol_sit_id != 3`.
     *
     * @return list<array<string, mixed>>
     */
    public function solPorAgenteAberta(int $userId): array
    {
        return $this->db->table('solicitacoes s')
            ->select('s.sid, s.criado, s.pedido, s.procedimento, s.responsavel, s.sol_sit_id, p.pes_id, p.pes_nome, p.pes_cidade, u.username AS cliente')
            ->join('pessoas p', 'p.pes_id = s.spessoa')
            ->join('users u', 'u.id = p.pes_cliente', 'left')
            ->where('s.sagente', $userId)
            ->where('s.sol_sit_id !=', 3)
            ->orderBy('s.criado', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Contagem de solicitações por cliente nos últimos N dias (gráficos de barras).
     *
     * Exclui os usuários de sistema (ids 76 e 74) e retorna `['username' => total]`.
     *
     * @return array<string, int>
     */
    public function solPorCliente(int $dias): array
    {
        $sql = 'SELECT u.username AS label, COUNT(*) AS total
                FROM solicitacoes s
                JOIN pessoas p ON p.pes_id = s.spessoa
                JOIN users u ON u.id = p.pes_cliente
                WHERE s.criado >= DATE_SUB(CURRENT_DATE(), INTERVAL ? DAY)
                  AND u.id NOT IN (76, 74)
                GROUP BY u.username
                ORDER BY total DESC';

        $rows = $this->db->query($sql, [$dias])->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $label         = ($row['label'] !== null && $row['label'] !== '') ? $row['label'] : 'sem cliente';
            $dados[$label] = (int) $row['total'];
        }

        return $dados;
    }

    /**
     * Contagem de solicitações por situação nos últimos N dias (gráfico doughnut).
     *
     * @return array<string, int>
     */
    public function solPorSituacao(int $dias): array
    {
        $sql = 'SELECT situacao AS label, COUNT(*) AS total
                FROM solicitacoes
                WHERE criado >= DATE_SUB(CURRENT_DATE(), INTERVAL ? DAY)
                GROUP BY situacao
                ORDER BY total DESC';

        $rows = $this->db->query($sql, [$dias])->getResultArray();

        $dados = [];
        foreach ($rows as $row) {
            $label         = ($row['label'] !== null && $row['label'] !== '') ? $row['label'] : 'Sem situação';
            $dados[$label] = (int) $row['total'];
        }

        return $dados;
    }

    protected function initialize(): void
    {
        parent::initialize();

        helper('funcoes');
    }

    /**
     * Listagem de solicitações com filtros (responsável, cliente, pessoa,
     * município e período) persistidos em sessão no controller.
     *
     * @param array<string, string> $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function consultar(array $filtros, int $limit, int $offset, bool $apenasAbertas = false): array
    {
        $builder = $this->builderBase($apenasAbertas);

        $this->aplicarFiltros($builder, $filtros);

        return $builder
            ->orderBy('s.criado', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Total de solicitações para a paginação (mesmos filtros da consulta).
     *
     * @param array<string, string> $filtros
     */
    public function contar(array $filtros, bool $apenasAbertas = false): int
    {
        $builder = $this->builderBase($apenasAbertas);

        $this->aplicarFiltros($builder, $filtros);

        $row = $builder->select('COUNT(*) AS total')->get()->getRow();

        return (int) ($row->total ?? 0);
    }

    /**
     * Solicitações de uma pessoa (accordion da tela de atendimento).
     *
     * @return list<array<string, mixed>>
     */
    public function daPessoa(int $pesId): array
    {
        return $this->db->table('solicitacoes s')
            ->select('s.*, u.username AS agente_nome')
            ->join('users u', 'u.id = s.sagente', 'left')
            ->where('s.spessoa', $pesId)
            ->orderBy('s.criado', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Aberturas de solicitação para o relatório de atividades (sem paginação).
     *
     * @param array<string, string> $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function aberturas(array $filtros): array
    {
        $builder = $this->db->table('solicitacoes s')
            ->select("'Abertura de solicitação' AS atividade, s.created, p.pes_nome AS nome, s.pedido")
            ->join('pessoas p', 'p.pes_id = s.spessoa');

        $this->aplicarFiltros($builder, $filtros);

        return $builder->get()->getResultArray();
    }

    /**
     * Base comum das consultas de solicitações (joins + colunas).
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function builderBase(bool $apenasAbertas)
    {
        $builder = $this->db->table('solicitacoes s')
            ->select('s.sid, s.spessoa, s.criado, s.pedido, s.procedimento, s.responsavel, s.sol_sit_id, p.pes_id, p.pes_nome, p.pes_cidade, u.username AS cliente')
            ->join('pessoas p', 'p.pes_id = s.spessoa')
            ->join('users u', 'u.id = p.pes_cliente', 'left');

        if ($apenasAbertas) {
            $builder->where('s.sol_sit_id !=', 3);
        }

        return $builder;
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

        if (! empty($filtros['data_inicio'])) {
            $builder->where('s.criado >=', $filtros['data_inicio']);
        }

        if (! empty($filtros['data_fim'])) {
            $builder->where('s.criado <=', data_final($filtros['data_fim']));
        }
    }
}
