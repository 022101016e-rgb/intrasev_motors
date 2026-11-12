<?php

namespace App\Controllers;

use App\Libraries\DisponibilidadService;
use App\Models\ClienteModel;
use App\Models\VehiculoModel;
use App\Models\BloqueHorarioModel;
use App\Models\SolicitudServicioModel;

class SolicitudController extends BaseController
{
    private function checkAuth()
    {
        if (!session()->has('id_usuario')) {
            session()->setFlashdata('error', 'Debes iniciar sesión para acceder al módulo de servicios.');
            return redirect()->to(site_url('login'));
        }
        return null;
    }

    private function clienteDeSesion()
    {
        $clienteModel = new ClienteModel();
        return $clienteModel->where('id_usuario', session()->get('id_usuario'))->first();
    }

    /**
     * DEF-03: un cliente solo puede ver sus propias solicitudes; el administrador ve todas.
     * Devuelve null (sin restricción) para el administrador, o el id_cliente propio (0 si no tiene perfil).
     */
    private function idClienteRestringido(): ?int
    {
        if (session()->get('id_rol') == 1) {
            return null;
        }
        $cliente = $this->clienteDeSesion();
        return $cliente ? (int) $cliente['id_cliente'] : 0;
    }

    public function index()
    {
        if ($r = $this->checkAuth()) return $r;

        $db = \Config\Database::connect();
        $builder = $db->table('tSolicitudesServicio t')
            ->select('t.codigo, t.fecha, t.hora, t.estado, t.detalle, u.nombre_completo, v.placa, v.marca, v.modelo, b.nombre AS bloque')
            ->join('tClientes c', 'c.id_cliente = t.id_cliente')
            ->join('tUsuarios u', 'u.id_usuario = c.id_usuario')
            ->join('tVehiculos v', 'v.id_vehiculo = t.id_vehiculo')
            ->join('tBloquesHorarios b', 'b.id_bloque = t.id_bloque');

        $restringido = $this->idClienteRestringido();
        if ($restringido !== null) {
            $builder->where('t.id_cliente', $restringido);
        }

        $solicitudes = $builder
            ->orderBy('t.id_solicitud', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return view('servicios/index', ['solicitudes' => $solicitudes]);
    }

    public function nuevo()
    {
        if ($r = $this->checkAuth()) return $r;

        $clienteModel = new ClienteModel();
        $vehiculoModel = new VehiculoModel();
        $bloqueModel = new BloqueHorarioModel();

        $esAdmin = session()->get('id_rol') == 1;

        $clientes = null;
        $clienteAsignado = null;
        $vehiculos = [];

        if ($esAdmin) {
            $clientes = $clienteModel->findAll();
        } else {
            $clienteAsignado = $this->clienteDeSesion();
            if (!$clienteAsignado) {
                session()->setFlashdata('error', 'Tu cuenta no tiene un perfil de cliente activo. Contacta al administrador.');
                return redirect()->to(site_url('servicios'));
            }
            $vehiculos = $vehiculoModel->where('id_cliente', $clienteAsignado['id_cliente'])->findAll();
        }

        return view('servicios/nuevo', [
            'clientes'        => $clientes,
            'clienteAsignado' => $clienteAsignado,
            'vehiculos'       => $vehiculos,
            'bloques'         => $bloqueModel->findAll(),
            'esAdmin'         => $esAdmin,
            'hoy'             => date('Y-m-d')
        ]);
    }

    public function vehiculosDeCliente($idCliente)
    {
        // DEF-02: antes era accesible sin sesión y para cualquier id_cliente.
        // Solo el administrador (que elige cliente en el formulario) necesita este endpoint.
        if (!session()->has('id_usuario')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Sesión requerida']);
        }
        if (session()->get('id_rol') != 1) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'No autorizado']);
        }

        $vehiculoModel = new VehiculoModel();
        return $this->response->setJSON($vehiculoModel->where('id_cliente', (int) $idCliente)->findAll());
    }

    public function guardar()
    {
        if ($r = $this->checkAuth()) return $r;

        $model = new SolicitudServicioModel();
        $clienteModel = new ClienteModel();
        $vehiculoModel = new VehiculoModel();
        $disponibilidad = new DisponibilidadService();
        $esAdmin = session()->get('id_rol') == 1;

        $id_cliente = (int) $this->request->getPost('id_cliente');
        if (!$esAdmin) {
            $clienteSesion = $this->clienteDeSesion();
            if (!$clienteSesion) {
                session()->setFlashdata('error', 'Tu cuenta no tiene un perfil de cliente activo.');
                return redirect()->to(site_url('servicios'));
            }
            $id_cliente = (int) $clienteSesion['id_cliente'];
        }

        $id_vehiculo = (int) $this->request->getPost('id_vehiculo');
        $fecha       = trim((string) $this->request->getPost('fecha'));
        $id_bloque   = (int) $this->request->getPost('id_bloque');
        // DEF-08: se normaliza (trim) antes de validar la longitud mínima/máxima.
        $detalle     = trim((string) $this->request->getPost('detalle'));

        // RF-01: validación de formulario (campos obligatorios y referencias válidas)
        $rules = [
            'id_vehiculo' => 'required|is_not_unique[tVehiculos.id_vehiculo]',
            'fecha'       => 'required|valid_date[Y-m-d]',
            'id_bloque'   => 'required|is_not_unique[tBloquesHorarios.id_bloque]',
            'detalle'     => 'required|min_length[3]|max_length[2000]',
        ];

        $datos = [
            'id_vehiculo' => $id_vehiculo,
            'fecha'       => $fecha,
            'id_bloque'   => $id_bloque,
            'detalle'     => $detalle,
        ];

        if (!$this->validateData($datos, $rules)) {
            $errores = array_keys($this->validator->getErrors());
            session()->setFlashdata('error', 'Datos inválidos. Revisa: ' . implode(', ', $errores));
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }

        // RF-02: la fecha no puede ser anterior a hoy
        if ($fecha < date('Y-m-d')) {
            session()->setFlashdata('error', 'La fecha de la solicitud no puede ser anterior a hoy.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }

        // RF-03: el cliente debe estar Activo y sin deudas
        $cliente = $clienteModel->find($id_cliente);
        if (!$cliente) {
            session()->setFlashdata('error', 'El cliente seleccionado no existe.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }
        if ($cliente['estado'] !== 'Activo') {
            session()->setFlashdata('error', 'El cliente está ' . $cliente['estado'] . '. Solo se aceptan solicitudes de clientes Activos.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }
        if ((float) $cliente['deudas'] > 0) {
            session()->setFlashdata('error', 'El cliente tiene una deuda pendiente de S/ ' . number_format($cliente['deudas'], 2) . '. No puede registrar nuevas solicitudes.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }

        // Integridad: el vehículo debe pertenecer al cliente
        $vehiculo = $vehiculoModel->find($id_vehiculo);
        if (!$vehiculo || (int) $vehiculo['id_cliente'] !== $id_cliente) {
            session()->setFlashdata('error', 'El vehículo seleccionado no pertenece al cliente.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }

        // RF-02 + RF-01 (DEF-04): disponibilidad y alta en una única transacción.
        // Se bloquea la fila del bloque (FOR UPDATE) para que dos solicitudes simultáneas
        // no superen la capacidad, y el código correlativo se deriva del id autoincremental
        // (único por diseño) en lugar de "último id + 1", que podía repetirse.
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $bloque = $db->query(
                'SELECT id_bloque, nombre, hora_inicio, hora_fin, capacidad FROM tBloquesHorarios WHERE id_bloque = ? FOR UPDATE',
                [$id_bloque]
            )->getRowArray();

            $count = $model->where('id_bloque', $id_bloque)->where('fecha', $fecha)->countAllResults();
            if (!$disponibilidad->hayCupo($count, (int) $bloque['capacidad'])) {
                $db->transRollback();
                session()->setFlashdata(
                    'error',
                    'Sin disponibilidad para el bloque ' . $bloque['nombre']
                    . ' (' . $bloque['hora_inicio'] . ' - ' . $bloque['hora_fin'] . '). '
                    . 'Ya hay ' . $count . ' solicitudes para esa fecha. Máximo permitido: ' . $bloque['capacidad'] . '.'
                );
                return redirect()->to(site_url('servicio/nuevo'))->withInput();
            }

            // Código provisional único; se reemplaza por SOL-NNNN usando el id recién generado.
            $idSolicitud = $model->insert([
                'codigo'         => 'TMP-' . bin2hex(random_bytes(6)),
                'id_cliente'     => $id_cliente,
                'id_vehiculo'    => $id_vehiculo,
                'id_bloque'      => $id_bloque,
                'fecha'          => $fecha,
                'hora'           => $bloque['hora_inicio'],
                'estado'         => 'Pendiente',
                'detalle'        => $detalle,
                'fecha_registro' => date('Y-m-d H:i:s'),
            ]);

            if (empty($idSolicitud)) {
                throw new \RuntimeException('No se pudo insertar la solicitud.');
            }

            $codigo = 'SOL-' . str_pad((string) $idSolicitud, 4, '0', STR_PAD_LEFT);
            $model->update($idSolicitud, ['codigo' => $codigo]);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error al registrar solicitud de servicio: ' . $e->getMessage());
            session()->setFlashdata('error', 'No fue posible completar la operación. Intenta nuevamente.');
            return redirect()->to(site_url('servicio/nuevo'))->withInput();
        }

        session()->setFlashdata('mensaje', 'Solicitud ' . $codigo . ' registrada correctamente para el '
            . date('d/m/Y', strtotime($fecha)) . '.');
        return redirect()->to(site_url('servicios'));
    }

    // RF-04: consultar solicitud por código correlativo o placa
    public function consultar()
    {
        if ($r = $this->checkAuth()) return $r;

        $codigo = trim($this->request->getGet('codigo') ?? '');
        $placa  = trim($this->request->getGet('placa') ?? '');

        $resultados = [];
        $criterio = '';

        if ($codigo !== '' || $placa !== '') {
            $db = \Config\Database::connect();
            $builder = $db->table('tSolicitudesServicio t')
                ->select('t.codigo, t.fecha, t.hora, t.estado, t.detalle, u.nombre_completo, u.correo_electronico, v.placa, v.marca, v.modelo, b.nombre AS bloque')
                ->join('tClientes c', 'c.id_cliente = t.id_cliente')
                ->join('tUsuarios u', 'u.id_usuario = c.id_usuario')
                ->join('tVehiculos v', 'v.id_vehiculo = t.id_vehiculo')
                ->join('tBloquesHorarios b', 'b.id_bloque = t.id_bloque');

            $restringido = $this->idClienteRestringido();
            if ($restringido !== null) {
                $builder->where('t.id_cliente', $restringido);
            }

            if ($codigo !== '') {
                $builder->where('t.codigo', strtoupper($codigo));
                $criterio = 'código: ' . $codigo;
            } else {
                $builder->where('v.placa', $placa);
                $criterio = 'placa: ' . $placa;
            }

            $resultados = $builder->orderBy('t.id_solicitud', 'DESC')->get()->getResultArray();
        }

        return view('servicios/consultar', [
            'resultados' => $resultados,
            'criterio'   => $criterio,
            'codigo'     => $codigo,
            'placa'      => $placa,
        ]);
    }
}