<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearClientes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cliente' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_usuario' => ['type' => 'INT', 'constraint' => 11],
            'estado' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Activo'],
            'deudas' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
        ]);
        $this->forge->addPrimaryKey('id_cliente');
        $this->forge->addForeignKey('id_usuario', 'tUsuarios', 'id_usuario', '', '', 'fk_clientes_usuario');
        $this->forge->createTable('tClientes', true);
    }

    public function down()
    {
        $this->forge->dropTable('tClientes', true);
    }
}
