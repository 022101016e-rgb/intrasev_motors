<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearProductos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_producto' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nombre' => ['type' => 'VARCHAR', 'constraint' => 150],
            'descripcion' => ['type' => 'TEXT', 'null' => true],
            'precio' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'imagen' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stock' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'estado_activo' => ['type' => 'TINYINT', 'constraint' => '4', 'default' => 1],
        ]);
        $this->forge->addPrimaryKey('id_producto');

        $this->forge->createTable('tProductos', true);
    }

    public function down()
    {
        $this->forge->dropTable('tProductos', true);
    }
}
