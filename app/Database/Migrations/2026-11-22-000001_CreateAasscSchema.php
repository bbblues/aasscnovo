<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Sprint 2 — recria o schema legado (`docs/_referencia/aassc.sql`).
 *
 * Tabelas, colunas, tipos, chaves primárias, índices e AUTO_INCREMENT são
 * fiéis ao dump. As tabelas Drupal (`node`, `field_data_comment_body`) NÃO
 * são recriadas. Destina-se a ambientes novos (dev/teste); em produção com
 * banco já existente, não rode esta migração de criação.
 */
class CreateAasscSchema extends Migration
{
    public function up(): void
    {
        // catpessoa (charset latin1 — inconsistência herdada do legado)
        $this->forge->addField([
            'cp_id'   => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true],
            'cp_nome' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->createTable('catpessoa', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'latin1', 'COLLATE' => 'latin1_swedish_ci']);

        // cidades
        $this->forge->addField([
            'cid'    => ['type' => 'INT', 'constraint' => 8, 'auto_increment' => true],
            'cinome' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('cid', true);
        $this->forge->createTable('cidades', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // comment (tabela Drupal reaproveitada para comentários/atualizações)
        $this->forge->addField([
            'cid'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'comment' => 'Primary Key: Unique comment ID.'],
            'nid'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'comment' => 'The node.nid to which this comment is a reply.'],
            'uid'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'comment' => 'The users.uid who authored the comment. If set to 0, this comment was created by an anonymous user.'],
            'subject' => ['type' => 'TEXT', 'comment' => 'The comment title.'],
            'created' => ['type' => 'INT', 'constraint' => 11, 'comment' => 'The time that the comment was created, as a Unix timestamp.'],
            'changed' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'comment' => 'The time that the comment was last edited, as a Unix timestamp.'],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'comment' => 'The comment author’s name. Uses users.name if the user is logged in, otherwise uses the value typed into the comment form.'],
        ]);
        $this->forge->addKey('cid', true);
        $this->forge->addKey('nid', false, false, 'idx_nid');
        $this->forge->createTable('comment', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci', 'COMMENT' => 'Stores comments and associated data.']);

        // groups (Ion Auth)
        $this->forge->addField([
            'id'          => ['type' => 'MEDIUMINT', 'constraint' => 8, 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'description' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->createTable('groups', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // login_attempts (Ion Auth)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 15],
            'login'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'time'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->createTable('login_attempts', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // pessoas
        $this->forge->addField([
            'pes_id'        => ['type' => 'INT', 'constraint' => 8, 'unsigned' => true, 'auto_increment' => true],
            'pes_nome'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pes_cliente'   => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'pes_cidade'    => ['type' => 'VARCHAR', 'constraint' => 25, 'null' => true],
            'sus'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'cpf'           => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'nascimento'    => ['type' => 'VARCHAR', 'constraint' => 34, 'null' => true],
            'endereco'      => ['type' => 'VARCHAR', 'constraint' => 92, 'null' => true],
            'mae'           => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'rg'            => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'uc'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'sexo'          => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'pes_user'      => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'pes_pub'       => ['type' => 'DATE', 'null' => true, 'default' => new RawSql('curdate()')],
            'pes_catpessoa' => ['type' => 'INT', 'constraint' => 9],
            'cep'           => ['type' => 'INT', 'constraint' => 9, 'null' => true],
        ]);
        $this->forge->addKey('pes_id', true);
        $this->forge->createTable('pessoas', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // procedimentos (termos reaproveitados do Drupal)
        $this->forge->addField([
            'med_id'   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true, 'comment' => 'Primary Key: Unique term ID.'],
            'med_nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '', 'comment' => 'The term name.'],
        ]);
        $this->forge->addKey('med_id', true);
        $this->forge->createTable('procedimentos', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci', 'COMMENT' => 'Stores term information.']);

        // situacoes
        $this->forge->addField([
            'sit_id'    => ['type' => 'INT', 'constraint' => 1, 'auto_increment' => true],
            'sit_nome'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'sit_icon'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'sit_color' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('sit_id', true);
        $this->forge->createTable('situacoes', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // solicitacoes
        $this->forge->addField([
            'sid'          => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'auto_increment' => true],
            'spessoa'      => ['type' => 'INT', 'constraint' => 9],
            'sagente'      => ['type' => 'INT', 'constraint' => 5, 'null' => true],
            'obs'          => ['type' => 'TEXT', 'null' => true],
            'pedido'       => ['type' => 'TEXT'],
            'procedimento' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'responsavel'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'situacao'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'sol_sit_id'   => ['type' => 'INT', 'constraint' => 1, 'unsigned' => true],
            'created'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'criado'       => ['type' => 'DATE'],
        ]);
        $this->forge->addKey('sid', true);
        $this->forge->createTable('solicitacoes', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // telefones (sem PK — apenas o índice idx_entity_id)
        $this->forge->addField([
            'entity_id'             => ['type' => 'INT', 'constraint' => 9],
            'field_telefones_value' => ['type' => 'VARCHAR', 'constraint' => 253, 'null' => true],
        ]);
        $this->forge->addKey('entity_id', false, false, 'idx_entity_id');
        $this->forge->createTable('telefones', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // users (Ion Auth)
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address'              => ['type' => 'VARCHAR', 'constraint' => 45],
            'username'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'password'                => ['type' => 'VARCHAR', 'constraint' => 255],
            'salt'                    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'activation_code'         => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'forgotten_password_code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'forgotten_password_time' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'remember_code'           => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'created_on'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1268889823],
            'last_login'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'active'                  => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'null' => true],
            'first_name'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'last_name'               => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'company'                 => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone'                   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);

        // users_groups (Ion Auth)
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'group_id' => ['type' => 'MEDIUMINT', 'constraint' => 8, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users_groups', false, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8', 'COLLATE' => 'utf8_general_ci']);
    }

    public function down(): void
    {
        $this->forge->dropTable('users_groups', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('telefones', true);
        $this->forge->dropTable('solicitacoes', true);
        $this->forge->dropTable('situacoes', true);
        $this->forge->dropTable('procedimentos', true);
        $this->forge->dropTable('pessoas', true);
        $this->forge->dropTable('login_attempts', true);
        $this->forge->dropTable('groups', true);
        $this->forge->dropTable('comment', true);
        $this->forge->dropTable('cidades', true);
        $this->forge->dropTable('catpessoa', true);
    }
}
