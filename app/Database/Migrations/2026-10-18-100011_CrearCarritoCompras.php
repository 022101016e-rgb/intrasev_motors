<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearCarritoCompras extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_carrito' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_usuario' => ['type' => 'INT', 'constraint' => 11],
            'id_producto' => ['type' => 'INT', 'constraint' => 11],
            'cantidad' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'fecha_agregado' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_carrito');
        $this->forge->addForeignKey('id_usuario', 'tUsuarios', 'id_usuario', '', '', 'fk_carrito_usuario');
        $this->forge->addForeignKey('id_producto', 'tProductos', 'id_producto', '', '', 'fk_carrito_producto');
        $this->forge->createTable('tCarritoCompras', true);
    }

    public function down()
    {
        $this->forge->dropTable('tCarritoCompras', true);
    }
}
