<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rol' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nombre_rol' => ['type' => 'VARCHAR', 'constraint' => 50],
            'descripcion' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_rol');

        $this->forge->createTable('tRoles', true);
    }

    public function down()
    {
        $this->forge->dropTable('tRoles', true);
    }
}
