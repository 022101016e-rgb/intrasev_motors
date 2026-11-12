<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearBloquesHorarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bloque' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nombre' => ['type' => 'VARCHAR', 'constraint' => 50],
            'hora_inicio' => ['type' => 'TIME'],
            'hora_fin' => ['type' => 'TIME'],
            'capacidad' => ['type' => 'INT', 'constraint' => 11, 'default' => 3],
        ]);
        $this->forge->addPrimaryKey('id_bloque');

        $this->forge->createTable('tBloquesHorarios', true);
    }

    public function down()
    {
        $this->forge->dropTable('tBloquesHorarios', true);
    }
}
