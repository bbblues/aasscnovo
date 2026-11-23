<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AuthSeeder — cria os grupos padrão (admin/agente/cliente) e um admin inicial.
 *
 * O dump de referência (`docs/_referencia/aassc.sql`) contém apenas estrutura,
 * sem dados. Este seeder destrava o primeiro login em ambientes novos.
 *
 *   php spark db:seed AuthSeeder
 *
 * Credenciais iniciais (altere após o primeiro login):
 *   e-mail: admin@aassc.com.br
 *   senha:  admin123
 */
class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // Grupos com id fixo, conforme docs/04-autenticacao-e-permissoes.md.
        $grupos = [
            ['id' => 1, 'name' => 'admin',   'description' => 'Administrador'],
            ['id' => 2, 'name' => 'agente',  'description' => 'Agente'],
            ['id' => 4, 'name' => 'cliente', 'description' => 'Cliente'],
        ];

        foreach ($grupos as $grupo) {
            $existe = $this->db->table('groups')->where('id', $grupo['id'])->get()->getRow();

            if ($existe === null) {
                $this->db->table('groups')->insert($grupo);
            }
        }

        // Admin inicial.
        $existeAdmin = $this->db->table('users')->where('email', 'admin@aassc.com.br')->get()->getRow();

        if ($existeAdmin === null) {
            $this->db->table('users')->insert([
                'ip_address' => '127.0.0.1',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'email'      => 'admin@aassc.com.br',
                'created_on' => time(),
                'active'     => 1,
                'first_name' => 'Administrador',
            ]);

            $userId = (int) $this->db->insertID();

            $this->db->table('users_groups')->insert([
                'user_id'  => $userId,
                'group_id' => 1,
            ]);
        }
    }
}
