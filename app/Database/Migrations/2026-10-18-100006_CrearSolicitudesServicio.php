<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearSolicitudesServicio extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_solicitud' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'codigo' => ['type' => 'VARCHAR', 'constraint' => 20],
            'id_cliente' => ['type' => 'INT', 'constraint' => 11],
            'id_vehiculo' => ['type' => 'INT', 'constraint' => 11],
            'id_bloque' => ['type' => 'INT', 'constraint' => 11],
            'fecha' => ['type' => 'DATE'],
            'hora' => ['type' => 'TIME'],
            'estado' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendiente'],
            'detalle' => ['type' => 'TEXT', 'null' => true],
            'fecha_registro' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_solicitud');
        $this->forge->addUniqueKey('codigo', 'uq_codigo');
        // Índice de apoyo para la consulta de disponibilidad (RF-02): bloque + fecha.
        $this->forge->addKey(['id_bloque', 'fecha'], false, false, 'idx_bloque_fecha');
        $this->forge->addForeignKey('id_cliente', 'tClientes', 'id_cliente', '', '', 'fk_solicitudes_cliente');
        $this->forge->addForeignKey('id_vehiculo', 'tVehiculos', 'id_vehiculo', '', '', 'fk_solicitudes_vehiculo');
        $this->forge->addForeignKey('id_bloque', 'tBloquesHorarios', 'id_bloque', '', '', 'fk_solicitudes_bloque');
        $this->forge->createTable('tSolicitudesServicio', true);
    }

    public function down()
    {
        $this->forge->dropTable('tSolicitudesServicio', true);
    }
}
