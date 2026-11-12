<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Datos de ejemplo/prueba de IntraServ Motors (equivalente a los INSERT de ds_g1.sql).
 *
 * Uso:   php spark db:seed DatosInicialesSeeder
 *
 * - Es idempotente: si tRoles ya tiene datos, no hace nada (evita duplicados).
 * - Las contraseñas se guardan HASHEADAS (password_hash), no en texto plano.
 * - SOLO para desarrollo/staging. Son credenciales ficticias de laboratorio.
 */
class DatosInicialesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('tRoles')->countAllResults() > 0) {
            echo "Seeder omitido: la base ya contiene datos (tRoles no está vacía).\n";
            return;
        }

        $ahora = date('Y-m-d H:i:s');

        // ---------- Roles ----------
        $this->db->table('tRoles')->insertBatch([
            ['id_rol' => 1, 'nombre_rol' => 'Administrador', 'descripcion' => 'Acceso total al panel de administración'],
            ['id_rol' => 2, 'nombre_rol' => 'Cliente',       'descripcion' => 'Puede comprar en la tienda'],
            ['id_rol' => 3, 'nombre_rol' => 'Ventas',        'descripcion' => 'Usuario de ventas sin acceso al panel de administración'],
        ]);

        // ---------- Usuarios (login / contraseña de prueba) ----------
        $usuarios = [
            [1, 'admin',    'Administrador Principal', 'admin@intrasev.com',    'admin123',    '999888777'],
            [2, 'cliente',  'Cliente Demo',            'cliente@intrasev.com',  'cliente123',  '987654321'],
            [1, 'richard',  'Richard',                 'richard@intrasev.com',  '123456',      '999000111'],
            [3, 'heral',    'Heral Ose',               'heral@intrasev.com',    '123456',      '888777666'],
            [2, 'deudor',   'Cliente deudor',          'deudor@intrasev.com',   'deudor123',   '900111222'],
            [2, 'inactivo', 'Cliente inactivo',        'inactivo@intrasev.com', 'inactivo123', '911222333'],
        ];
        foreach ($usuarios as [$rol, $user, $nombre, $correo, $clave, $tel]) {
            $this->db->table('tUsuarios')->insert([
                'id_rol'             => $rol,
                'usuario'            => $user,
                'nombre_completo'    => $nombre,
                'correo_electronico' => $correo,
                'contrasena_hash'    => password_hash($clave, PASSWORD_DEFAULT),
                'telefono'           => $tel,
                'fecha_creacion'     => $ahora,
            ]);
        }

        $idUsuario = static fn ($db, string $u): int => (int) $db->table('tUsuarios')
            ->select('id_usuario')->where('usuario', $u)->get()->getRow()->id_usuario;

        // ---------- Clientes (módulo de Servicios) ----------
        $this->db->table('tClientes')->insertBatch([
            ['id_usuario' => $idUsuario($this->db, 'cliente'),  'estado' => 'Activo',   'deudas' => 0.00],
            ['id_usuario' => $idUsuario($this->db, 'deudor'),   'estado' => 'Activo',   'deudas' => 500.00],
            ['id_usuario' => $idUsuario($this->db, 'inactivo'), 'estado' => 'Inactivo', 'deudas' => 0.00],
        ]);

        // ---------- Vehículos ----------
        $this->db->table('tVehiculos')->insertBatch([
            ['id_cliente' => 1, 'placa' => 'ABC-123', 'marca' => 'Toyota', 'modelo' => 'Corolla',  'anio' => 2015],
            ['id_cliente' => 1, 'placa' => 'XYZ-789', 'marca' => 'VW',     'modelo' => 'Golf MK3', 'anio' => 1998],
            ['id_cliente' => 2, 'placa' => 'DEF-456', 'marca' => 'Kia',    'modelo' => 'Picanto',  'anio' => 2018],
            ['id_cliente' => 3, 'placa' => 'GHI-000', 'marca' => 'Honda',  'modelo' => 'Civic',    'anio' => 2000],
        ]);

        // ---------- Bloques horarios (capacidad 3 por bloque y fecha) ----------
        $this->db->table('tBloquesHorarios')->insertBatch([
            ['nombre' => 'Mañana A', 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00', 'capacidad' => 3],
            ['nombre' => 'Mañana B', 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00', 'capacidad' => 3],
            ['nombre' => 'Tarde A',  'hora_inicio' => '14:00:00', 'hora_fin' => '16:00:00', 'capacidad' => 3],
            ['nombre' => 'Tarde B',  'hora_inicio' => '16:00:00', 'hora_fin' => '18:00:00', 'capacidad' => 3],
        ]);

        // ---------- Solicitudes de ejemplo (RF-04) ----------
        $this->db->table('tSolicitudesServicio')->insertBatch([
            [
                'codigo' => 'SOL-0001', 'id_cliente' => 1, 'id_vehiculo' => 1, 'id_bloque' => 1,
                'fecha' => date('Y-m-d', strtotime('+1 day')), 'hora' => '08:00:00', 'estado' => 'Pendiente',
                'detalle' => 'Cambio de aceite y filtros.', 'fecha_registro' => $ahora,
            ],
            [
                'codigo' => 'SOL-0002', 'id_cliente' => 1, 'id_vehiculo' => 2, 'id_bloque' => 2,
                'fecha' => date('Y-m-d', strtotime('+2 days')), 'hora' => '10:00:00', 'estado' => 'Pendiente',
                'detalle' => 'Reprogramación Stage 1.', 'fecha_registro' => $ahora,
            ],
        ]);

        // ---------- Tienda ----------
        $this->db->table('tProductos')->insertBatch([
            ['nombre' => 'Kit de Frenos Cerámicos',   'descripcion' => 'Kit de pastillas y discos de competición.',      'precio' => 450.00, 'imagen' => null, 'stock' => 25, 'estado_activo' => 1],
            ['nombre' => 'Intercooler Frontal',       'descripcion' => 'Intercooler de mayor capacidad para turbo.',      'precio' => 680.00, 'imagen' => null, 'stock' => 12, 'estado_activo' => 1],
            ['nombre' => 'Escape Deportivo Acero',    'descripcion' => 'Sistema de escape completo en acero inoxidable.', 'precio' => 890.00, 'imagen' => null, 'stock' => 8,  'estado_activo' => 1],
            ['nombre' => 'Filtro de Aire Deportivo',  'descripcion' => 'Filtro de alto flujo reutilizable.',              'precio' => 120.00, 'imagen' => null, 'stock' => 40, 'estado_activo' => 1],
        ]);

        $this->db->table('tAutos')->insertBatch([
            ['modelo' => 'Honda Civic 1998',   'descripcion' => 'Proyecto JDM con motor VTEC.',                        'url_imagen' => null],
            ['modelo' => 'VW Golf MK3',        'descripcion' => 'Reprogramación Stage1 y suspensión coilover.',        'url_imagen' => null],
            ['modelo' => 'Subaru Impreza 98',  'descripcion' => 'AWD reforzado con turbo y rally setup.',              'url_imagen' => null],
        ]);

        echo "Datos iniciales cargados correctamente.\n";
    }
}
