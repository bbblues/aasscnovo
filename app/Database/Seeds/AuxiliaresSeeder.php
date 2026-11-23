<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AuxiliaresSeeder — popula as tabelas de apoio usadas nos selects
 * (situacoes, catpessoa, cidades e procedimentos) com dados de exemplo.
 *
 * O dump de referência contém apenas estrutura. Este seeder destrava a
 * validação ponta a ponta da Sprint 5 em ambientes novos.
 *
 *   php spark db:seed AuxiliaresSeeder
 *
 * Os valores são apenas para desenvolvimento e devem ser substituídos pelos
 * dados reais antes da produção.
 */
class AuxiliaresSeeder extends Seeder
{
    public function run(): void
    {
        // Situações (referência; o código usa situacao_by_id() para exibição).
        foreach ([
            ['sit_id' => 1, 'sit_nome' => 'Aguardando Resp', 'sit_icon' => '', 'sit_color' => ''],
            ['sit_id' => 2, 'sit_nome' => 'Em Andamento',   'sit_icon' => '', 'sit_color' => ''],
            ['sit_id' => 3, 'sit_nome' => 'Finalizado',     'sit_icon' => '', 'sit_color' => ''],
        ] as $situacao) {
            $this->inserirSeAusente('situacoes', 'sit_id', $situacao);
        }

        // Categoria de pessoa (pes_catpessoa NOT NULL).
        $this->inserirSeAusente('catpessoa', 'cp_id', ['cp_id' => 1, 'cp_nome' => 'Pessoa física']);

        // Cidades de exemplo.
        foreach (['Araraquara', 'São Carlos', 'Matão', 'Américo Brasiliense', 'Rincão'] as $cidade) {
            $this->inserirSeAusente('cidades', 'cinome', ['cinome' => $cidade]);
        }

        // Procedimentos de exemplo.
        foreach (['Consulta', 'Exame', 'Cirurgia', 'Medicação', 'Transporte'] as $procedimento) {
            $this->inserirSeAusente('procedimentos', 'med_nome', ['med_nome' => $procedimento]);
        }
    }

    /**
     * Insere o registro apenas quando a chave informada ainda não existe.
     *
     * @param array<string, mixed> $dados
     */
    private function inserirSeAusente(string $tabela, string $chave, array $dados): void
    {
        $existe = $this->db->table($tabela)
            ->where($chave, $dados[$chave])
            ->get()
            ->getRow();

        if ($existe === null) {
            $this->db->table($tabela)->insert($dados);
        }
    }
}
