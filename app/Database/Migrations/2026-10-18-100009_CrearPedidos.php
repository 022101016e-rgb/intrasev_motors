<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearPedidos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pedido' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_usuario' => ['type' => 'INT', 'constraint' => 11],
            'fecha_pedido' => ['type' => 'DATETIME'],
            'estado_pedido' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendiente'],
            'monto_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
        ]);
        $this->forge->addPrimaryKey('id_pedido');
        $this->forge->addForeignKey('id_usuario', 'tUsuarios', 'id_usuario', '', '', 'fk_pedidos_usuario');
        $this->forge->createTable('tPedidos', true);
    }

    public function down()
    {
        $this->forge->dropTable('tPedidos', true);
    }
}
