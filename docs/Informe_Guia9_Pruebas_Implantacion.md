# Informe integrado — Guía 9 (Semanas 10 y 11)
## Pruebas de sistema, gestión de defectos e implantación en entorno de pruebas

**Asignatura:** Análisis y Diseño de Sistemas de Información II
**Proyecto:** IntraServ Motors (CodeIgniter 4.5.1) — **Módulo evaluado:** Servicios / Solicitudes de servicio (RF-01 a RF-04)

> **Estado del documento (leer primero).** Este informe fue preparado a partir de la **revisión del código y de la documentación del módulo**. Las pruebas en navegador, la ejecución de migraciones y el despliegue **aún no se han ejecutado**: la columna *Obtenido* y los estados **PASS/FAIL** de F1, F2, F3, F8 y el checklist F7 están **vacíos a propósito** (estado `NOT RUN`). La guía exige no inventar PASS. Complete esos campos al ejecutar cada paso y adjunte capturas numeradas.
> Los defectos de la sección 4 se detectaron por **revisión estática**; quedan en estado *Listo para re-prueba* (no *Cerrado*) hasta que se repita el escenario.

---

## 1. Identificación

| Campo | Registro |
|---|---|
| Proyecto / módulo | IntraServ Motors / Servicios (solicitudes de servicio) |
| Integrantes | [completar] |
| Versión candidata | `v0.2-rc1` (sugerida) — commit: `[completar: git rev-parse --short HEAD]` |
| Fechas | [completar] |
| URL controlada (staging) | `http://localhost:8081/` |
| Requisitos | RF-01 Registrar solicitud · RF-02 Disponibilidad (fecha ≥ hoy, capacidad por bloque) · RF-03 Cliente Activo y sin deuda · RF-04 Consultar por código o placa |

---

## 2. Pruebas de sistema (caja negra)

### 2.1 Particiones de equivalencia

| Entrada | Partición | Representante | Resultado esperado |
|---|---|---|---|
| Fecha | P1 hoy o futura | hoy+1 | Se acepta |
| | P2 anterior a hoy | ayer | Rechaza: *La fecha de la solicitud no puede ser anterior a hoy.* |
| | P3 vacía / formato inválido | `""` / `31-02-2026` | Rechaza: `Datos inválidos. Revisa: fecha` |
| Cliente | P1 Activo y deuda 0 | `cliente` (id 1) | Se acepta |
| | P2 Activo con deuda > 0 | `deudor` (id 2, S/ 500) | Rechaza por deuda |
| | P3 Inactivo | `inactivo` (id 3) | Rechaza por estado |
| Detalle | P1 3–2000 caracteres | "Cambio de aceite" | Se acepta |
| | P2 < 3 | "ab" | Rechaza: `Datos inválidos. Revisa: detalle` |
| | P3 > 2000 | 2001 caracteres | Rechaza: `Datos inválidos. Revisa: detalle` |
| | P4 vacío / solo espacios | `"   "` | Rechaza |
| Bloque y fecha | P1 con cupo (< capacidad) | Mañana A, hoy+1 (1 de 3) | Se acepta |
| | P2 lleno (= capacidad) | bloque con 3 solicitudes | Rechaza: *Sin disponibilidad…* |
| Consulta | P1 código existente | `SOL-0001` | 1 resultado |
| | P2 placa existente | `ABC-123` | Solicitudes del vehículo |
| | P3 criterio inexistente | `SOL-9999` | *No se encontraron solicitudes para código: SOL-9999.* |

### 2.2 Valores frontera

| Variable | Valores | Esperado |
|---|---|---|
| Longitud de `detalle` | 2 · **3** · **2000** · 2001 | rechaza · acepta · acepta · rechaza |
| Solicitudes ya registradas en el bloque/fecha (capacidad 3) | 2 · **3** | acepta la 3.ª · rechaza la 4.ª |
| Fecha | ayer · **hoy** · mañana | rechaza · acepta · acepta |
| Deuda del cliente | 0.00 · **0.01** | acepta · rechaza |

> La capacidad (3) y los mensajes provienen de `docs/Documentacion_Modulo_Servicios.md` y del código; si el requisito vigente cambia, la tabla debe cambiar.

### 2.3 Tabla de decisión (¿se crea la solicitud?)
El orden refleja el primer impedimento que detecta `SolicitudController::guardar()` (tras la validación de formulario).

| Condición / acción | R1 | R2 | R3 | R4 | R5 | R6 |
|---|---|---|---|---|---|---|
| C1 fecha ≥ hoy | N | S | S | S | S | S |
| C2 cliente Activo | – | N | S | S | S | S |
| C3 cliente sin deuda | – | – | N | S | S | S |
| C4 vehículo pertenece al cliente | – | – | – | N | S | S |
| C5 cupo disponible en el bloque | – | – | – | – | N | S |
| **Crear solicitud `SOL-NNNN` (Pendiente)** | N | N | N | N | N | **S** |
| Rechazar con mensaje | S | S | S | S | S | N |

Casos derivados: **R3 → CP-06**, **R2 → CP-07**, **R5 → CP-05**, **R6 → CP-01**.

### 2.4 F1 — Registro de casos ejecutados
Precondición común: BD de prueba con `php spark migrate` + `php spark db:seed DatosInicialesSeeder`. D+1 = hoy + 1 día. Navegador: [completar].

| ID | Req. | Entrada y precondición | Esperado | Obtenido | Estado |
|---|---|---|---|---|---|
| CP-01 | RF-01 | Login `cliente`/`cliente123`. Vehículo ABC-123, fecha D+1, bloque Tarde A, detalle "Cambio de aceite" | Redirige a `/servicios` con *Solicitud SOL-0003 registrada correctamente para el …*; aparece en la lista con estado Pendiente | | NOT RUN |
| CP-02 | RF-01 (frontera) | Igual a CP-01 en otra fecha (D+3); detalle exactamente 3 caracteres: `abc` | Se acepta (límite inferior) | | NOT RUN |
| CP-03 | RF-01 (frontera/neg.) | Detalle de 2 caracteres: `ab` | Rechaza: `Datos inválidos. Revisa: detalle`; no se crea registro | | NOT RUN |
| CP-04 | RF-02 (neg.) | Fecha de ayer (el `min` del input lo impide en pantalla: quitarlo con DevTools) | *La fecha de la solicitud no puede ser anterior a hoy.* | | NOT RUN |
| CP-05 | RF-02 (frontera/tabla R5) | Bloque Mañana B en D+2 (ya tiene SOL-0002). Crear 2 más (deben aceptarse: 2.ª y 3.ª). Intentar una 4.ª | La 4.ª se rechaza: *Sin disponibilidad … Ya hay 3 solicitudes … Máximo permitido: 3.* | | NOT RUN |
| CP-06 | RF-03 (neg./tabla R3) | Login `deudor`/`deudor123`, vehículo DEF-456, datos válidos | *El cliente tiene una deuda pendiente de S/ 500.00 …* sin registro nuevo | | NOT RUN |
| CP-07 | RF-03 (neg./tabla R2) | Login `inactivo`/`inactivo123`, vehículo GHI-000, datos válidos | *El cliente está Inactivo. Solo se aceptan solicitudes de clientes Activos.* | | NOT RUN |
| CP-08 | RF-04 | Login `cliente`. (a) Consultar placa `ABC-123`; (b) consultar código `SOL-9999` | (a) lista las solicitudes de ABC-123; (b) *No se encontraron solicitudes para código: SOL-9999.* | | NOT RUN |

Cobertura: positivos CP-01, CP-02, CP-08a · negativos CP-03, CP-04, CP-06, CP-07, CP-08b · límite CP-02, CP-03, CP-05 · tabla de decisión CP-05, CP-06, CP-07.

---

## 3. E2E y aceptación

### F2 — Escenarios E2E

**E2E-01 (válido) — RF-01/RF-03/RF-04**

| Campo | Contenido |
|---|---|
| Meta del usuario | Un cliente registra una solicitud de servicio y la encuentra después |
| Precondiciones | BD con seeder; cliente `cliente` activo, sin deuda, vehículo ABC-123; cupo libre en Tarde A (D+1) |
| Pasos | 1) Iniciar sesión · 2) Ir a *Servicios → Nueva solicitud* · 3) ABC-123, D+1, Tarde A, "Cambio de aceite" · 4) Registrar · 5) Ver mensaje y lista · 6) Consultar por el código mostrado · 7) Verificar en BD |
| Resultado esperado | Mensaje de éxito con código `SOL-NNNN`; la solicitud aparece una vez, estado Pendiente; `SELECT COUNT(*), estado FROM tSolicitudesServicio WHERE codigo='SOL-NNNN';` → 1, `Pendiente` |
| Resultado observado | |
| Evidencia | [captura N.º, consulta, fecha/hora] |
| Estado | NOT RUN |

**E2E-02 (inválido) — RF-03**

| Campo | Contenido |
|---|---|
| Meta del usuario | Un cliente con deuda intenta registrar una solicitud |
| Precondiciones | `SELECT COUNT(*) FROM tSolicitudesServicio WHERE id_cliente=2;` → valor inicial N₀ |
| Pasos | Login `deudor` → Nueva solicitud → DEF-456, D+1, Mañana A, detalle válido → Registrar |
| Resultado esperado | Mensaje de deuda; el formulario conserva lo escrito; el recuento de la consulta sigue siendo N₀ |
| Resultado observado | |
| Evidencia | [captura N.º, consulta, fecha/hora] |
| Estado | NOT RUN |

### F3 — UAT y usabilidad (intercambio con otro equipo)
Requiere a un equipo externo y 1–2 usuarios invitados. Tareas neutrales sugeridas (no nombrar botones):

| Tarea | Objetivo y observación | Completó | Mejora sugerida |
|---|---|---|---|
| T-01 "Pida un servicio para su vehículo para mañana por la mañana" | [qué intentó, dónde dudó] | Sí / No | |
| T-02 "Averigüe en qué estado está la solicitud SOL-0001" | [ ] | Sí / No | |

**Dictamen UAT:** acepta / acepta con observaciones / no acepta — motivo: [completar]
**Criterios satisfechos / no satisfechos:** [completar]

*Puntos a observar (hipótesis de la revisión, a confirmar con usuarios reales; no son hallazgos todavía):* el selector de cliente del administrador muestra solo el id (sin nombre); el formulario no indica cuántos cupos quedan en cada bloque; no hay alta de vehículos desde la interfaz.

---

## 4. Defectos

> **Origen:** revisión estática del código original. Re-prueba pendiente en todos. "Obtenido" describe lo que el código original hacía.

### F5 — Matriz resumen

| ID | Descripción breve | Sever. | Prioridad | Estado / evidencia |
|---|---|---|---|---|
| DEF-01 | Contraseñas guardadas y comparadas en texto plano (login, registro, admin, datos de ejemplo) | Alta | Alta | Corregido en código → **Listo para re-prueba** |
| DEF-02 | `GET /servicio/vehiculos/{id}` respondía sin sesión y para cualquier cliente | Alta | Alta | Corregido → Listo para re-prueba |
| DEF-03 | Cualquier usuario con sesión veía solicitudes y correos de todos los clientes | Media | Alta | Corregido (confirmar criterio de diseño) → Listo para re-prueba |
| DEF-04 | Código `SOL-NNNN` = último id + 1 y control de capacidad "consultar y luego insertar" sin bloqueo | Media | Media | Corregido (transacción + `FOR UPDATE`; código derivado del id) → Listo para re-prueba |
| DEF-05 | Formulario `servicio/guardar` sin token CSRF | Media | Media | Corregido (filtro `csrf` + `csrf_field()`) → Listo para re-prueba |
| DEF-06 | El registro de cliente no creaba su fila en `tClientes`: el módulo respondía *"Tu cuenta no tiene un perfil de cliente activo"* | Media | Alta | Corregido → Listo para re-prueba |
| DEF-07 | Registro sin validación; correo repetido provocaba excepción (error 500) por la clave única | Media | Media | Corregido → Listo para re-prueba |
| DEF-08 | `detalle` no se normalizaba: `"ab "` superaba el mínimo de 3 y se guardaba con espacios | Baja | Baja | Corregido → Listo para re-prueba |

### F4 — Fichas (versión y entorno de todas: commit `[completar]`, navegador `[completar]`, URL `http://localhost:8081`)

**DEF-01 — Contraseñas en texto plano** · Req.: transversal (acceso) · Caso: E2E-01 (login)
Precond.: BD cargada con `ds_g1.sql` original. Pasos: 1) `SELECT usuario, contrasena_hash FROM tUsuarios;` 2) iniciar sesión con `admin`/`admin123`. Esperado: la BD no contiene contraseñas legibles. Obtenido (código original): `contrasena_hash` = `admin123`; `AuthController::login` comparaba con `===`. Severidad **Alta** (vulneración de credenciales ante cualquier lectura de BD o respaldo); prioridad **Alta** (antes de la candidata).
Corrección: `password_hash()` en seeder, registro y alta de admin; `password_verify()` en login; las cuentas antiguas en texto plano se aceptan **una vez** y se re-guardan hasheadas.
Re-prueba: tras `db:seed`, `contrasena_hash` empieza con `$2y$`; login correcto entra; clave errónea se rechaza; usuario creado por registro/admin también queda hasheado.

**DEF-02 — Endpoint de vehículos sin control de acceso** · RF-01 · Caso: CP-01
Pasos: sin iniciar sesión, abrir `/servicio/vehiculos/1`. Esperado: 401/redirección. Obtenido (original): JSON con placa, marca y modelo. Severidad **Alta** (exposición de datos de terceros; id enumerable); prioridad **Alta**.
Corrección: 401 sin sesión, 403 si no es administrador (solo el admin usa el selector dinámico).
Re-prueba: `curl -i http://localhost:8081/servicio/vehiculos/1` → 401; con sesión de cliente → 403; con admin → 200 y el formulario del admin sigue cargando vehículos al cambiar de cliente.

**DEF-03 — Datos de otros clientes visibles** · RF-04 · Caso: CP-08
Pasos: login `deudor` → `/servicios` y consultar `ABC-123`. Esperado: no ver solicitudes de otro cliente. Obtenido (original): lista de las últimas 20 solicitudes de todos, con nombre; la consulta incluía correo. Severidad **Media**; prioridad **Alta** (datos personales).
Corrección: el cliente solo ve las propias; el administrador ve todas. Consulta por código ahora es **exacta** (antes `LIKE`).
Re-prueba: `deudor` no ve SOL-0001/0002 y `ABC-123` devuelve *No se encontraron…*; `admin` las ve.
*Decisión a confirmar:* si el requisito exige que cualquier usuario consulte cualquier solicitud, revertir esta restricción y documentarlo como riesgo aceptado.

**DEF-04 — Código y capacidad no atómicos** · RF-01/RF-02 · Casos: CP-01, CP-05
Escenario: dos envíos simultáneos para el mismo bloque con 2 de 3 cupos, o dos altas a la vez. Esperado: máximo 3 por bloque/fecha y códigos únicos. Obtenido (análisis del código original): ambos pasan el conteo y se insertan 4; o intentan el mismo `SOL-NNNN` y falla por `uq_codigo`. Severidad **Media**; prioridad **Media**.
Corrección: transacción, bloqueo de la fila del bloque (`SELECT … FOR UPDATE`), alta con código provisional y actualización a `SOL-` + id.
Re-prueba: regresión CP-01 y CP-05; opcional, dos pestañas enviando casi a la vez.

**DEF-05 — Sin protección CSRF** · RF-01 · Caso: CP-01
Pasos: `curl -i -X POST http://localhost:8081/servicio/guardar -d "id_vehiculo=1&fecha=…"` con cookie válida y sin token. Esperado: rechazo. Obtenido (original): se procesaba. Severidad **Media**; prioridad **Media**.
Corrección: filtro `csrf` en esa ruta + `csrf_field()` en el formulario.
Re-prueba: POST sin token → rechazado (403/pantalla de error); el formulario normal sigue registrando (CP-01).

**DEF-06 — Cliente registrado sin perfil de cliente** · RF-01 · Caso: E2E (registro → solicitud)
Pasos: `/registro` → crear cuenta → login → *Nueva solicitud*. Esperado: poder iniciar el flujo. Obtenido (original): *Tu cuenta no tiene un perfil de cliente activo. Contacta al administrador.* Severidad **Media**; prioridad **Alta** (afecta aceptación).
Corrección: el registro inserta en `tClientes` (Activo, deuda 0) en la misma transacción que el usuario.
Re-prueba: la cuenta nueva ya no recibe ese mensaje (verá *No hay vehículos cargados*: ver riesgo R-02).

**DEF-07 — Registro sin validación** · Caso: registro
Pasos: registrar un correo que ya existe. Esperado: mensaje claro. Obtenido (original): excepción por la clave única `uq_correo`. Severidad **Media**; prioridad **Media**.
Corrección: reglas (usuario 3–50, correo válido y único, clave ≥ 6) y transacción con manejo de error.
Re-prueba: correo repetido y usuario repetido muestran mensaje; no hay error 500.

**DEF-08 — `detalle` sin normalizar** · RF-01 · Caso: CP-03
Pasos: detalle `"ab "`. Esperado: rechazo (2 caracteres útiles). Obtenido (original): longitud 3 → aceptado, guardado con espacios. Severidad **Baja**; prioridad **Baja**.
Corrección: `trim()` antes de validar y de guardar.
Re-prueba: `"ab "` se rechaza; `"  abc "` se guarda como `abc`.

**Criterio de cierre (guía):** un defecto pasa a *Cerrado* solo con commit correctivo, reproducción previa o evidencia equivalente, re-prueba exitosa, revisión de regresión y decisión registrada. Hasta entonces todos permanecen *Listo para re-prueba*.

### Riesgos y observaciones abiertas (no corregidos; fuera del módulo o del alcance)

| ID | Riesgo |
|---|---|
| R-01 | CSRF activo solo en `servicio/guardar`. Login, registro, panel admin y carrito no lo tienen (activarlo globalmente exige añadir el token a cada formulario y a las llamadas `fetch`). |
| R-02 | No existe alta de vehículos en la interfaz: un cliente nuevo no puede registrar solicitudes. |
| R-03 | Las eliminaciones de productos y administradores se hacen por **GET** (`admin/*/eliminar/{id}`). |
| R-04 | Las rutas `/carrito/*` no tienen filtro de autenticación en `Routes.php` (no se revisó cada método del controlador); existe la ruta de diagnóstico `carrito/test`, que no debería publicarse. |
| R-05 | Las vistas cargan Bootstrap, iconos y fuentes desde CDN: sin Internet en staging la interfaz se degrada. |
| R-06 | El seeder crea cuentas ficticias con claves débiles (`admin123`…). Solo para laboratorio; no usar en un servidor accesible. |
| R-07 | Ningún cambio de esta entrega se ha ejecutado todavía (sin PHP/BD en el entorno de preparación). |

---

## 5. Despliegue

### 5.1 Qué se preparó en el repositorio
- **Migraciones** (`app/Database/Migrations/`, 11 archivos `2026-10-18-1000NN_*`): `tRoles`, `tUsuarios`, `tClientes`, `tVehiculos`, `tBloquesHorarios`, `tSolicitudesServicio` (con índice `idx_bloque_fecha`), `tProductos`, `tAutos`, `tPedidos`, `tDetallePedidos`, `tCarritoCompras`; con claves únicas y foráneas equivalentes a `ds_g1.sql`.
- **Seeder** `DatosInicialesSeeder` (idempotente, contraseñas hasheadas).
- **Endpoint** `GET /salud` → `{"estado":"ok"}`.
- **Plantillas** `env` y `env.staging.example` (el `.env` real sigue ignorado por Git).
- **`writable/`**: subcarpetas `cache`, `logs`, `session`, `debugbar`, `uploads` con su `.htaccess` (el `.gitignore` las daba por existentes, pero no venían en el repositorio, así que un clon limpio no las tenía).
- **Scripts**: `scripts/deploy_staging.(sh|ps1)`, `deploy_staging_continuar.sh`, `smoke_test.(sh|ps1)`.

### 5.2 Procedimiento (Pasos 1–7 de la guía)
```bash
# En desarrollo: identificar el commit exacto (Paso 1)
git status && git rev-parse --short HEAD && git log -1 --oneline
git tag v0.2-rc1            # opcional

# Copia independiente + dependencias desde composer.lock (Pasos 2-3)
git clone <URL_DEL_REPOSITORIO> proyecto-staging && cd proyecto-staging
composer install --no-dev --prefer-dist --optimize-autoloader

# BD distinta (Paso 4) — en MySQL/MariaDB:
#   CREATE DATABASE ds_g1_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# .env en destino (Paso 5) — copiar env.staging.example a .env y poner credenciales reales
cp env .env                 # Windows CMD: copy env .env

# Migraciones (Paso 6) — verificar ANTES que .env apunta a ds_g1_staging
php spark migrate:status
php spark migrate
php spark migrate:status
php spark db:seed DatosInicialesSeeder     # solo laboratorio

# Levantar (Paso 7)
php spark serve --port 8081
```
> Si se ejecuta `php spark migrate` sobre una BD ya creada con `ds_g1.sql`, las migraciones usan `createTable(..., true)` (IF NOT EXISTS), por lo que no destruyen tablas existentes; el script SQL sigue sirviendo para reiniciar la BD de desarrollo.

### F6 — Manifiesto de la versión desplegada

| Campo | Registro |
|---|---|
| Proyecto y módulo | IntraServ Motors — Servicios |
| Versión / commit | `v0.2-rc1` / `[completar]` |
| Fecha y responsable | [completar] |
| URL del destino | `http://localhost:8081/` |
| PHP / CodeIgniter | PHP ≥ 8.2 (`composer.json`; versión real: [completar `php -v`]) / CodeIgniter 4.5.1 |
| Método de instalación | Clon + Composer (`--no-dev`) + migraciones |
| Base de datos | `ds_g1_staging` (distinta de `ds_g1`) — sin credenciales |
| Configuración ajustada | `CI_ENVIRONMENT`, `app.baseURL`, `database.default.database/username/password/hostname/port/DBDriver` |
| Cambios relevantes | Migraciones y seeder; `/salud`; contraseñas con hash; control de acceso del endpoint de vehículos; alcance por cliente en listado/consulta; transacción en alta de solicitudes; CSRF en el formulario de solicitudes; validación y perfil de cliente en el registro; normalización de `detalle` |
| Riesgos y plan de retorno | Riesgos R-01…R-07. Retorno: `git checkout <tag/commit anterior>` + `composer install`; restaurar el volcado previo (`mysqldump ds_g1_staging > respaldo.sql` **antes** de migrar). Cuidado: los `down()` de las migraciones eliminan tablas (pérdida de datos); en staging es aceptable. |

### F7 — Checklist de despliegue
(☑ solo lo verificable en los archivos entregados; el resto se marca al ejecutar)

- ☐ El commit o versión está identificado.
- ☐ Se conocen defectos bloqueantes/pendientes y decisión de liberar (ver sección 6).
- ☐ El entorno de staging es independiente del de desarrollo.
- ☐ La base de datos de staging es distinta.
- ☐ Se instaló con `composer install` sin actualizar el lockfile.
- ☑ El `.env` local no se publica en Git (`.gitignore` contiene `.env`; se entregan solo `env` y `env.staging.example`).
- ☐ Se verificó URL, puerto y variables de BD.
- ☐ Se verificaron migraciones y tablas (`migrate:status`, `SHOW TABLES`).
- ☐ La ruta principal es accesible.
- ☐ El flujo crítico funciona desde el navegador.
- ☐ Se ejecutaron pruebas de humo y se registraron resultados.
- ☐ Se revisaron errores relevantes en `writable/logs/` sin exponer secretos.
- ☐ Existe un procedimiento básico de retorno o restauración (descrito en F6; validar).

---

## 6. Pruebas de humo

Parte automática: `scripts/smoke_test.sh http://localhost:8081` (o `scripts\smoke_test.ps1 -Base …`). Las acciones SM-03 y SM-04 se hacen en el navegador.

### F8 — Resultados

| ID | Acción y respuesta esperada | Resultado obtenido | Estado |
|---|---|---|---|
| SM-01 | `GET /salud` → HTTP 200 y `{"estado":"ok"}` | | NOT RUN |
| SM-02 | `GET /` → 200 (la portada consulta `tProductos`/`tAutos`: BD disponible) | | NOT RUN |
| SM-03 | Operación válida (CP-01) confirma y persiste (consulta SQL) | | NOT RUN |
| SM-04 | Operación inválida (CP-06) rechaza y no persiste | | NOT RUN |
| SM-05 | `writable/logs/`: sin error crítico no explicado | | NOT RUN |

Adicionales del script: `/login` → 200; `/servicios` sin sesión → 302; `/servicio/vehiculos/1` sin sesión → 401 (verifica DEF-02).

**Guion para la validación en vivo (A8):** (1) mostrar commit/tag; (2) diferencias desarrollo/staging: `.env` (entorno, URL/puerto, BD); (3) ejecutar un humo (`/salud`); (4) `php spark migrate:status` para citar las migraciones aplicadas; (5) abrir `writable/logs/`; (6) `.env` está en `.gitignore` y la raíz web debe ser `public/`; (7) retorno: checkout del commit anterior + restauración del respaldo.

---

## 7. Conclusión técnica y decisión de continuidad

- **Decisión provisional: *Corregir y re-probar* → *Listo para staging* cuando se cumplan las condiciones de abajo.** No se autoriza producción.
- **Condiciones para pasar a "Listo para staging":** (a) ejecutar CP-01…CP-08 y E2E-01/02 y anotar resultados reales; (b) re-probar DEF-01…DEF-08; (c) completar F3 con otro equipo; (d) ejecutar F8 en la copia desplegada. Si falla una operación esencial (CP-01, CP-06, login) o aparece pérdida de datos, la decisión es *Corregir y reprobar*.
- **Para producción faltaría:** cerrar R-01…R-04, servidor web con `DocumentRoot` en `public/`, HTTPS, respaldos y monitoreo, credenciales reales fuera de Git.
- **Responsabilidades:** [completar por integrante].
- **Evidencias:** capturas numeradas y fechadas `[CAP-01 …]`, asociadas a un caso o defecto.
