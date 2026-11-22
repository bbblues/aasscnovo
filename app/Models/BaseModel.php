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
        if (! self::$bigSelectsApplied) {
            $this->db->query('SET SESSION SQL_BIG_SELECTS=1');
            self::$bigSelectsApplied = true;
        }
    }
}
