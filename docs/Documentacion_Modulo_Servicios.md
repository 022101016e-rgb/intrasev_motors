# Módulo de Servicios — Solicitudes de Servicio (IntraServ Motors)

**Proyecto:** IntraServ Motors (CodeIgniter 4, MySQL/MariaDB)
**Fecha de implementación:** 2026-09-24
**Estado:** Implementado

---

## 1. Objetivo del módulo

Permitir a los clientes registrar **solicitudes de servicio** para sus vehículos
en el taller, validando disponibilidad de horario y el estado del cliente,
y permitir consultar solicitudes por **código correlativo** o por **placa**.

## 2. Requisitos funcionales cubiertos

| ID | Requisito | Estado |
|----|-----------|--------|
| RF-01 | Registrar solicitud (cliente, vehículo, fecha/hora, detalle, estado inicial "Pendiente") | ✅ Implementado |
| RF-02 | Validar disponibilidad (máx. 3 solicitudes por bloque horario, fecha no anterior a hoy) | ✅ Implementado |
| RF-03 | Validar estado del cliente ("Activo" y sin deudas) | ✅ Implementado |
| RF-04 | Consultar solicitud por código correlativo o placa | ✅ Implementado |

## 3. Estructura del módulo (MVC)

| Capa | Archivo |
|------|---------|
| Controlador | `app/Controllers/SolicitudController.php` |
| Modelos | `app/Models/ClienteModel.php`, `app/Models/VehiculoModel.php`, `app/Models/BloqueHorarioModel.php`, `app/Models/SolicitudServicioModel.php` |
| Vistas | `app/Views/servicios/index.php`, `app/Views/servicios/nuevo.php`, `app/Views/servicios/consultar.php` |
| Rutas | `app/Config/Routes.php` |
| Base de datos | `app/Database/ds_g1.sql` |

## 4. Base de datos (tablas)

### tClientes
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_cliente | INT PK AUTO | Clave primaria |
| id_usuario | INT FK -> tUsuarios | Usuario asociado (rol 2) |
| estado | VARCHAR(20) | `Activo` / `Inactivo` (RF-03) |
| deudas | DECIMAL(10,2) | Monto pendiente; `0.00` = sin deuda (RF-03) |

### tVehiculos
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_vehiculo | INT PK AUTO | Clave primaria |
| id_cliente | INT FK -> tClientes | Dueño del vehículo |
| placa | VARCHAR(15) UNIQUE | Placa (RF-04) |
| marca / modelo / anio | ... | Datos del vehículo |

### tBloquesHorarios
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_bloque | INT PK AUTO | Clave primaria |
| nombre | VARCHAR(50) | Ej. `Mañana A` |
| hora_inicio / hora_fin | TIME | Rango del bloque |
| capacidad | INT (def. 3) | Máx. solicitudes por bloque y fecha (RF-02) |

### tSolicitudesServicio
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_solicitud | INT PK AUTO | Clave primaria |
| codigo | VARCHAR(20) UNIQUE | Código correlativo `SOL-0001` (RF-04) |
| id_cliente / id_vehiculo / id_bloque | INT FK | Relaciones |
| fecha / hora | DATE / TIME | Fecha y hora del servicio (RF-01) |
| estado | VARCHAR(20) def. `Pendiente` | Estado inicial (RF-01) |
| detalle | TEXT | Descripción del servicio (RF-01) |
| fecha_registro | DATETIME | Momento del registro |

## 5. Rutas

| Método | Ruta | Acción |
|--------|------|--------|
| GET | `/servicios` | Listado de solicitudes |
| GET | `/servicio/nuevo` | Formulario de registro |
| POST | `/servicio/guardar` | Guardar solicitud (RF-01, RF-02, RF-03) |
| GET | `/servicio/consultar` | Consulta por código o placa (RF-04) |
| GET | `/servicio/vehiculos/{id_cliente}` | JSON vehículos del cliente (formulario) |

Todas requieren sesión iniciada (validación interna de `SolicitudController`).
Si el usuario es cliente (rol 2), su perfil se toma automáticamente; si es
administrador (rol 1), puede seleccionar cualquier cliente del listado.

## 6. Validaciones implementadas

### RF-01 — Registro de solicitud
- Reglas de formulario (`SolicitudController::guardar()`):
  `id_vehiculo`, `fecha`, `id_bloque` y `detalle` obligatorios (**$this->validate()**).
  - `id_vehiculo` → `is_not_unique[tVehiculos.id_vehiculo]`
  - `id_bloque` → `is_not_unique[tBloquesHorarios.id_bloque]`
  - `fecha` → `valid_date[Y-m-d]`
  - `detalle` → `min_length[3]|max_length[2000]`
- Estado inicial **`Pendiente`** y código correlativo `SOL-NNNN` generado automáticamente.
- Mensaje de éxito:
  > `Solicitud SOL-0003 registrada correctamente para el <fecha>.`

### RF-02 — Disponibilidad
- **Fecha no anterior a hoy:** si `fecha < hoy` → error:
  > `La fecha de la solicitud no puede ser anterior a hoy.`
- **Máx. 3 (capacidad) por bloque y fecha:** cuenta solicitudes con
  `id_bloque = X AND fecha = Y`; si `count >= capacidad` → error:
  > `Sin disponibilidad para el bloque <nombre> (<hora_inicio> - <hora_fin>). Ya hay <n> solicitudes para esa fecha. Máximo permitido: <capacidad>.`

### RF-03 — Estado del cliente
Consulta `tClientes` y rechaza:
- Si `estado != 'Activo'` →
  > `El cliente está <Inactivo>. Solo se aceptan solicitudes de clientes Activos.`
- Si `deudas > 0` →
  > `El cliente tiene una deuda pendiente de S/ <monto>. No puede registrar nuevas solicitudes.`
- Validación de integridad adicional: el vehículo debe pertenecer al cliente →
  > `El vehículo seleccionado no pertenece al cliente.`

### RF-04 — Consulta
- Por **código** (`codigo` LIKE) o por **placa** (`v.placa = ...`).
- Sin resultados →
  > `No se encontraron solicitudes para <código|placa>.`

## 7. Datos de prueba (seeds en ds_g1.sql)

| Usuario login / pass | Rol | Cliente | Estado | Deudas |
|----------------------|-----|---------|--------|--------|
| `cliente / cliente123` | 2 | id 1 | Activo | 0.00 |
| `deudor / deudor123` | 2 | id 2 | Activo | 500.00 |
| `inactivo / inactivo123` | 2 | id 3 | Inactivo | 0.00 |

Vehículos: `ABC-123` (Toyota), `XYZ-789` (VW) del cliente 1; `DEF-456` (Kia) del cliente 2; `GHI-000` (Honda) del cliente 3.
Bloques: Mañana A (08-10), Mañana B (10-12), Tarde A (14-16), Tarde B (16-18), capacidad 3 c/u.
Solicitudes ejemplo: `SOL-0001` y `SOL-0002` (cliente 1).

## 8. Cómo preparar el ambiente

```bash
mysql -u root < app/Database/ds_g1.sql
```

Verificar en `app/Config/Database.php` que la conexión use la base `ds_g1`
y credenciales correctas (o definir `.env`). Levantar con el servidor local
(php spark serve o XAMPP) e ingresar como `cliente`, `deudor`, `inactivo` o `admin`.

## 9. Casos de prueba sugeridos (positivo / negativo / frontera)

| # | Tipo | Datos | Resultado esperado |
|---|------|-------|--------------------|
| 1 | Positivo | cliente 1, vehículo ABC-123, fecha mañana, bloque Mañana A, detalle válido | Solicitud creada, estado Pendiente, código SOL-0003 |
| 2 | Negativo | fecha anterior a hoy | Error "La fecha de la solicitud no puede ser anterior a hoy." |
| 3 | Negativo | cliente inactivo (id 3) | Error "El cliente está Inactivo..." |
| 4 | Negativo | cliente deudor (id 2) | Error "El cliente tiene una deuda pendiente..." |
| 5 | Negativo | 10 solicitudes para mismo bloque+fecha (>= capacidad) | Error "Sin disponibilidad..." |
| 6 | Frontera | exactamente capacidad (3) igual a 3 registrados | Se rechaza la 4.ª solicitud |
| 7 | Positivo | consultar codigo `SOL-0001` | Devuelve la solicitud |
| 8 | Positivo | consultar placa `ABC-123` | Devuelve solicitudes del vehículo |
| 9 | Negativo | consultar código inexistente | "No se encontraron solicitudes..." |

## 10. Archivos modificados / creados

**Creados**
- `app/Controllers/SolicitudController.php`
- `app/Models/ClienteModel.php`
- `app/Models/VehiculoModel.php`
- `app/Models/BloqueHorarioModel.php`
- `app/Models/SolicitudServicioModel.php`
- `app/Views/servicios/index.php`
- `app/Views/servicios/nuevo.php`
- `app/Views/servicios/consultar.php`
- `docs/Documentacion_Modulo_Servicios.md` (este documento)

**Modificados**
- `app/Config/Routes.php` (rutas del módulo)
- `app/Views/header.php` (enlace *Servicios* en el menú)
- `app/Database/ds_g1.sql` (tablas y datos de prueba)