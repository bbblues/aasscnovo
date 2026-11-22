<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Sprint 2 — índices aditivos de performance.
 *
 * Seguros: não alteram dados e podem ser aplicados sobre um banco já
 * importado do dump. `comment.nid` já possui `idx_nid` no dump; aqui só
 * acrescentamos `comment.changed`.
 */
class AddPerformanceIndexes extends Migration
{
    public function up(): void
    {
        $indexes = [
            'solicitacoes' => [
                'idx_spessoa'    => 'spessoa',
                'idx_sagente'    => 'sagente',
                'idx_sol_sit_id' => 'sol_sit_id',
                'idx_criado'     => 'criado',
            ],
            'pessoas' => [
                'idx_pes_cliente' => 'pes_cliente',
                'idx_pes_pub'     => 'pes_pub',
            ],
            'comment' => [
                'idx_changed' => 'changed',
            ],
        ];

        foreach ($indexes as $table => $list) {
            foreach ($list as $indexName => $column) {
                $this->db->query(sprintf(
                    'CREATE INDEX %s ON %s (%s)',
                    $this->db->escapeIdentifiers($indexName),
                    $this->db->escapeIdentifiers($table),
                    $this->db->escapeIdentifiers($column),
                ));
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'solicitacoes' => ['idx_spessoa', 'idx_sagente', 'idx_sol_sit_id', 'idx_criado'],
            'pessoas'      => ['idx_pes_cliente', 'idx_pes_pub'],
            'comment'      => ['idx_changed'],
        ];

        foreach ($indexes as $table => $list) {
            foreach ($list as $indexName) {
                $this->forge->dropKey($table, $indexName);
            }
        }
    }
}
