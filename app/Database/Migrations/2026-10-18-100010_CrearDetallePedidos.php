<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearDetallePedidos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detalle' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_pedido' => ['type' => 'INT', 'constraint' => 11],
            'id_producto' => ['type' => 'INT', 'constraint' => 11],
            'cantidad' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'precio_unitario_historico' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
        ]);
        $this->forge->addPrimaryKey('id_detalle');
        $this->forge->addForeignKey('id_pedido', 'tPedidos', 'id_pedido', '', '', 'fk_detalle_pedido');
        $this->forge->addForeignKey('id_producto', 'tProductos', 'id_producto', '', '', 'fk_detalle_producto');
        $this->forge->createTable('tDetallePedidos', true);
    }

    public function down()
    {
        $this->forge->dropTable('tDetallePedidos', true);
    }
}
