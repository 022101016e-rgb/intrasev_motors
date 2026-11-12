<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_usuario' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_rol' => ['type' => 'INT', 'constraint' => 11],
            'usuario' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nombre_completo' => ['type' => 'VARCHAR', 'constraint' => 150],
            'correo_electronico' => ['type' => 'VARCHAR', 'constraint' => 150],
            'contrasena_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'telefono' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'fecha_creacion' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_usuario');
        $this->forge->addUniqueKey('usuario', 'uq_usuario');
        $this->forge->addUniqueKey('correo_electronico', 'uq_correo');
        $this->forge->addForeignKey('id_rol', 'tRoles', 'id_rol', '', '', 'fk_usuarios_rol');
        $this->forge->createTable('tUsuarios', true);
    }

    public function down()
    {
        $this->forge->dropTable('tUsuarios', true);
    }
}
