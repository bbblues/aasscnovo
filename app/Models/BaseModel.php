<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model base do projeto.
 *
 * Aplica `SET SESSION SQL_BIG_SELECTS=1` uma única vez por requisição,
 * preservando o comportamento do legado (que executava a instrução no
 * construtor de todos os Models de consulta) para grandes volumes de dados.
 */
abstract class BaseModel extends Model
{
    /**
     * Evita reexecutar a instrução a cada instância de Model.
     */
    private static bool $bigSelectsApplied = false;

    protected function initialize(): void
    {
        // `SQL_BIG_SELECTS` é específico do MySQL/MariaDB. Em outros drivers
        // (ex.: SQLite usado nos testes) a instrução não existe e quebraria.
        if (! self::$bigSelectsApplied && $this->db->DBDriver === 'MySQLi') {
            $this->db->query('SET SESSION SQL_BIG_SELECTS=1');
            self::$bigSelectsApplied = true;
        }
    }
}
