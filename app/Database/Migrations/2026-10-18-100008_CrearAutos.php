<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearAutos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_auto' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'modelo' => ['type' => 'VARCHAR', 'constraint' => 150],
            'descripcion' => ['type' => 'TEXT', 'null' => true],
            'url_imagen' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_auto');

        $this->forge->createTable('tAutos', true);
    }

    public function down()
    {
        $this->forge->dropTable('tAutos', true);
    }
}
