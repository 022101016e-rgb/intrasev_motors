<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearVehiculos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_vehiculo' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_cliente' => ['type' => 'INT', 'constraint' => 11],
            'placa' => ['type' => 'VARCHAR', 'constraint' => 15],
            'marca' => ['type' => 'VARCHAR', 'constraint' => 60],
            'modelo' => ['type' => 'VARCHAR', 'constraint' => 60],
            'anio' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_vehiculo');
        $this->forge->addUniqueKey('placa', 'uq_placa');
        $this->forge->addForeignKey('id_cliente', 'tClientes', 'id_cliente', '', '', 'fk_vehiculos_cliente');
        $this->forge->createTable('tVehiculos', true);
    }

    public function down()
    {
        $this->forge->dropTable('tVehiculos', true);
    }
}
