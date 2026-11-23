<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Schema SQLite-compatível (somente para testes) das tabelas do domínio.
 *
 * A migração de produção (app/Database/Migrations) usa recursos específicos do
 * MySQL/MariaDB (ENGINE/CHARSET/MEDIUMINT/TINYINT/curdate()), que não são
 * aceitos pelo SQLite em memória usado pelo PHPUnit. Este arquivo espelha as
 * mesmas tabelas/colunas com tipos portáveis, para que os testes de Models e
 * de autenticação rodem em qualquer ambiente (host ou Docker).
 *
 * Os ids são informados explicitamente pelo Sprint7TestSeeder, então as chaves
 * primárias não dependem de AUTO_INCREMENT.
 */
class CreateTestSchema extends Migration
{
    public function up(): void
    {
        // groups (Ion Auth)
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER'],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'description' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('groups');

        // login_attempts (Ion Auth)
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER'],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 15],
            'login'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'time'       => ['type' => 'INTEGER', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('login_attempts');

        // users (Ion Auth)
        $this->forge->addField([
            'id'                      => ['type' => 'INTEGER'],
            'ip_address'              => ['type' => 'VARCHAR', 'constraint' => 45],
            'username'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'password'                => ['type' => 'VARCHAR', 'constraint' => 255],
            'salt'                    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'activation_code'         => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'forgotten_password_code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'forgotten_password_time' => ['type' => 'INTEGER', 'null' => true],
            'remember_code'           => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'created_on'              => ['type' => 'INTEGER', 'null' => true],
            'last_login'              => ['type' => 'INTEGER', 'null' => true],
            'active'                  => ['type' => 'INTEGER', 'null' => true],
            'first_name'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'last_name'               => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'company'                 => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone'                   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // users_groups (Ion Auth)
        $this->forge->addField([
            'id'       => ['type' => 'INTEGER'],
            'user_id'  => ['type' => 'INTEGER'],
            'group_id' => ['type' => 'INTEGER'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users_groups');

        // pessoas
        $this->forge->addField([
            'pes_id'        => ['type' => 'INTEGER'],
            'pes_nome'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pes_cliente'   => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'pes_cidade'    => ['type' => 'VARCHAR', 'constraint' => 25, 'null' => true],
            'sus'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'cpf'           => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'nascimento'    => ['type' => 'VARCHAR', 'constraint' => 34, 'null' => true],
            'endereco'      => ['type' => 'VARCHAR', 'constraint' => 92, 'null' => true],
            'mae'           => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'rg'            => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'uc'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sexo'          => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'pes_user'      => ['type' => 'INTEGER', 'null' => true],
            'pes_pub'       => ['type' => 'DATE', 'null' => true],
            'pes_catpessoa' => ['type' => 'INTEGER', 'null' => true],
            'cep'           => ['type' => 'INTEGER', 'null' => true],
        ]);
        $this->forge->addKey('pes_id', true);
        $this->forge->createTable('pessoas');

        // telefones (sem PK no dump; apenas o índice idx_entity_id)
        $this->forge->addField([
            'entity_id'             => ['type' => 'INTEGER'],
            'field_telefones_value' => ['type' => 'VARCHAR', 'constraint' => 253, 'null' => true],
        ]);
        $this->forge->addKey('entity_id', false, false, 'idx_entity_id');
        $this->forge->createTable('telefones');

        // solicitacoes
        $this->forge->addField([
            'sid'          => ['type' => 'INTEGER'],
            'spessoa'      => ['type' => 'INTEGER', 'null' => true],
            'sagente'      => ['type' => 'INTEGER', 'null' => true],
            'obs'          => ['type' => 'TEXT', 'null' => true],
            'pedido'       => ['type' => 'TEXT', 'null' => true],
            'procedimento' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'responsavel'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'situacao'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sol_sit_id'   => ['type' => 'INTEGER', 'null' => true],
            'created'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'criado'       => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('sid', true);
        $this->forge->createTable('solicitacoes');

        // comment (tabela Drupal reaproveitada para comentários/atualizações)
        $this->forge->addField([
            'cid'     => ['type' => 'INTEGER'],
            'nid'     => ['type' => 'INTEGER', 'default' => 0],
            'uid'     => ['type' => 'INTEGER', 'default' => 0],
            'subject' => ['type' => 'TEXT', 'null' => true],
            'created' => ['type' => 'INTEGER', 'null' => true],
            'changed' => ['type' => 'INTEGER', 'default' => 0],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
        ]);
        $this->forge->addKey('cid', true);
        $this->forge->addKey('nid', false, false, 'idx_nid');
        $this->forge->createTable('comment');
    }

    public function down(): void
    {
        $this->forge->dropTable('comment', true);
        $this->forge->dropTable('solicitacoes', true);
        $this->forge->dropTable('telefones', true);
        $this->forge->dropTable('pessoas', true);
        $this->forge->dropTable('users_groups', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('login_attempts', true);
        $this->forge->dropTable('groups', true);
    }
}
