<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Sprint7TestSeeder — dados determinísticos para os testes de Models e da
 * autenticação (Sprint 7).
 *
 * Os ids são fixos para facilitar as asserções. Senhas usam `password_hash`
 * (bcrypt), como no sistema real. A inserção é feita registro a registro para
 * aceitar linhas com colunas opcionais diferentes (ex.: `last_name` ausente).
 */
class Sprint7TestSeeder extends Seeder
{
    public function run(): void
    {
        // Grupos (ids fixos conforme docs/04).
        $this->inserir('groups', [
            ['id' => 1, 'name' => 'admin',   'description' => 'Administrador'],
            ['id' => 2, 'name' => 'agente',  'description' => 'Agente'],
            ['id' => 4, 'name' => 'cliente', 'description' => 'Cliente'],
        ]);

        // Usuários.
        $this->inserir('users', [
            [
                'id'         => 10,
                'ip_address' => '127.0.0.1',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'email'      => 'admin@aassc.com.br',
                'created_on' => 1700000000,
                'active'     => 1,
                'first_name' => 'Administrador',
            ],
            [
                'id'         => 20,
                'ip_address' => '127.0.0.1',
                'username'   => 'agente',
                'password'   => password_hash('agente123', PASSWORD_BCRYPT),
                'email'      => 'agente@aassc.com.br',
                'created_on' => 1700000000,
                'active'     => 1,
                'first_name' => 'Agente',
                'last_name'  => 'Um',
            ],
            [
                'id'         => 30,
                'ip_address' => '127.0.0.1',
                'username'   => 'cliente_a',
                'password'   => password_hash('cliente123', PASSWORD_BCRYPT),
                'email'      => 'cliente@aassc.com.br',
                'created_on' => 1700000000,
                'active'     => 1,
                'first_name' => 'Cliente',
                'last_name'  => 'Um',
            ],
            [
                'id'         => 40,
                'ip_address' => '127.0.0.1',
                'username'   => 'inativo',
                'password'   => password_hash('inativo123', PASSWORD_BCRYPT),
                'email'      => 'inativo@aassc.com.br',
                'created_on' => 1700000000,
                'active'     => 0,
                'first_name' => 'Inativo',
            ],
        ]);

        $this->inserir('users_groups', [
            ['id' => 1, 'user_id' => 10, 'group_id' => 1],
            ['id' => 2, 'user_id' => 20, 'group_id' => 2],
            ['id' => 3, 'user_id' => 30, 'group_id' => 4],
            ['id' => 4, 'user_id' => 40, 'group_id' => 4],
        ]);

        // Pessoas (pes_cliente referencia users.id como string).
        $this->inserir('pessoas', [
            [
                'pes_id'      => 100,
                'pes_nome'    => 'Ana Souza',
                'pes_cliente' => '30',
                'pes_cidade'  => 'Araraquara',
                'email'       => 'ana@example.com',
                'pes_user'    => 20,
                'pes_pub'     => '2026-01-10',
            ],
            [
                'pes_id'      => 101,
                'pes_nome'    => 'Bruno Lima',
                'pes_cliente' => '30',
                'pes_cidade'  => 'São Carlos',
                'email'       => 'bruno@example.com',
                'pes_user'    => 20,
                'pes_pub'     => '2026-02-15',
            ],
            [
                'pes_id'      => 102,
                'pes_nome'    => 'Carla Mendes',
                'pes_cliente' => '30',
                'pes_cidade'  => 'Araraquara',
                'email'       => 'carla@example.com',
                'pes_user'    => 20,
                'pes_pub'     => '2026-03-20',
            ],
        ]);

        $this->inserir('telefones', [
            ['entity_id' => 100, 'field_telefones_value' => '16 99999-0001'],
            ['entity_id' => 101, 'field_telefones_value' => '16 99999-0002'],
            ['entity_id' => 102, 'field_telefones_value' => '16 99999-0003'],
        ]);

        // Solicitações.
        $this->inserir('solicitacoes', [
            [
                'sid'          => 1000,
                'spessoa'      => 100,
                'sagente'      => 20,
                'pedido'       => 'Pedido de consulta A',
                'procedimento' => 'Consulta',
                'responsavel'  => 'Ana',
                'situacao'     => 'Em Andamento',
                'sol_sit_id'   => 2,
                'created'      => '1700000000',
                'criado'       => '2026-01-10',
            ],
            [
                'sid'          => 1001,
                'spessoa'      => 101,
                'sagente'      => 20,
                'pedido'       => 'Pedido de exame B',
                'procedimento' => 'Exame',
                'responsavel'  => 'Bruno',
                'situacao'     => 'Finalizado',
                'sol_sit_id'   => 3,
                'created'      => '1700000100',
                'criado'       => '2026-02-15',
            ],
            [
                'sid'          => 1002,
                'spessoa'      => 102,
                'sagente'      => 20,
                'pedido'       => 'Pedido de consulta C',
                'procedimento' => 'Consulta',
                'responsavel'  => 'Carla',
                'situacao'     => 'Aguardando Resp',
                'sol_sit_id'   => 1,
                'created'      => '1700000200',
                'criado'       => '2026-03-20',
            ],
        ]);

        // Comentários (atualizações).
        $this->inserir('comment', [
            ['cid' => 2000, 'nid' => 1000, 'uid' => 20, 'subject' => 'Comentário A', 'created' => 1700001000, 'changed' => 1700001000, 'name' => 'agente'],
            ['cid' => 2001, 'nid' => 1000, 'uid' => 20, 'subject' => 'Comentário B', 'created' => 1700002000, 'changed' => 1700002000, 'name' => 'agente'],
            ['cid' => 2002, 'nid' => 1001, 'uid' => 20, 'subject' => 'Comentário C', 'created' => 1700003000, 'changed' => 1700003000, 'name' => 'agente'],
        ]);
    }

    /**
     * @param list<array<string, mixed>> $linhas
     */
    private function inserir(string $tabela, array $linhas): void
    {
        foreach ($linhas as $linha) {
            $this->db->table($tabela)->insert($linha);
        }
    }
}
