INTRASEV MOTORS

Sistema web de venta de autopartes y proyectos de autos, desarrollado con **CodeIgniter 4** bajo el patrón **MVC**.

> Documentación técnica del proyecto: módulos, arquitectura y modelo de datos.

---

##  Tabla de contenidos

1. [Descripción general](#-descripción-general)
2. [Stack tecnológico](#-stack-tecnológico)
3. [Estructura MVC](#-estructura-mvc)
4. [Módulos principales](#-módulos-principales)
5. [Diagrama de arquitectura](#-diagrama-de-arquitectura)
6. [Modelo lógico de datos](#-modelo-lógico-de-datos)
7. [Guía de instalación / puesta en marcha local](#-guía-de-instalación--puesta-en-marcha-local)
8. [Credenciales de prueba](#-credenciales-de-prueba)
9. [Rutas principales](#-rutas-principales)

---

##  Descripción general

INTRASEV es una **tienda virtual de autopartes y showcase de proyectos de autos** (JDM, rally, etc.).
Incluye:

- **Tienda pública** con catálogo de productos y vitrina de autos.
- **Autenticación** de usuarios (clientes y administradores).
- **Panel de administración** para la gestión de productos y administradores.
- **Carrito de compras** y flujo de pedidos (pendiente / pagado / cancelado).

---

##  Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend / Framework | CodeIgniter 4 (PHP ≥ 8.1, recomendado 8.2) |
| Base de datos | MySQL / MariaDB (MySQLi) |
| Frontend | PHP Views + HTML5 + CSS3 + JavaScript |
| Estilos / JS | Bootstrap 5, Bootstrap Icons, fuentes Google |
| Entorno local | XAMPP (PHP 8.2, MariaDB 10.4) |

---

##  Estructura MVC

El proyecto sigue el patrón **Modelo-Vista-Controlador** de CodeIgniter 4.

```
public_html/
├── app/
│   ├── Config/          → Configuración (rutas, BD, filtros, autenticación)
│   ├── Controllers/     → CONTROLADORES: Home, Auth, Admin, Carrito
│   ├── Models/          → MODELOS: entidades de la base de datos
│   ├── Views/           → VISTAS: tienda, login, registro, panel admin, carrito
│   ├── Database/        → Script SQL de la base de datos (ds_g1.sql)
│   ├── Filters/         → Filtros (adminAuth, etc.)
│   └── ...
├── public/              → Documento raíz público (index.php, assets, uploads)
├── system/              → Núcleo del framework CodeIgniter 4
├── writable/            → Archivos escribibles (logs, sesiones, cache)
├── .env                 → Variables de entorno (URL base y conexión a BD)
└── spark                → Ejecutable CLI de CodeIgniter
```

### Capas

| Capa | Ubicación | Responsabilidad |
|------|-----------|-----------------|
| **Modelo** | `app/Models/` | Acceso y manipulación de datos (CRUD) |
| **Vista** | `app/Views/` | Presentación/interfaz de usuario |
| **Controlador** | `app/Controllers/` | Lógica de negocio y orquestación |

---

##  Módulos principales

### 1. Autenticación (`AuthController`)
| Ítem | Detalle |
|------|---------|
| Descripción | Registro, inicio y cierre de sesión de usuarios |
| Vistas | `login.php`, `registro.php` |
| Modelos | `UsuarioModel`, `RolModel` |
| Rutas | `/login`, `/auth/login`, `/registro`, `/auth/registrar`, `/auth/logout` |
| Función clave | `registrar()`: alta de nuevos clientes (rol 2) con persistencia |

### 2. Tienda pública (`Home`)
| Ítem | Detalle |
|------|---------|
| Descripción | Página principal: exhibe productos y vitrina de autos |
| Vistas | `index.php` |
| Modelos | `ProductoModel`, `AutoModel` |
| Rutas | `/` |
| Función clave | `index()`: consulta `findAll()` de productos y autos |

### 3. Panel de Administración (`AdminController`)
| Ítem | Detalle |
|------|---------|
| Descripción | Gestión de productos y de administradores |
| Vistas | `admin_dashboard.php`, `admin/productos.php`, `admin/usuarios.php` |
| Modelos | `ProductoModel`, `UsuarioModel` |
| Rutas | `/admin/dashboard`, `/admin/productos*`, `/admin/usuarios*` |
| Protección | Grupo de rutas con filtro `adminAuth` |
| Funciones clave | `guardarProducto()`, `guardarUsuario()`, `eliminarProducto()`, `eliminarUsuario()` |

### 4. Carrito de Compras (`CarritoController`)
| Ítem | Detalle |
|------|---------|
| Descripción | Carrito, checkout y procesamiento de pedidos |
| Vistas | `checkout.php`, `checkout_exito.php` |
| Modelos | `CarritoComprasModel`, `ProductoModel`, `PedidoModel`, `DetallePedidoModel` |
| Rutas | `/carrito/*` (agregar, obtener, actualizar, eliminar, vaciar, checkout, procesar_pago) |
| Funciones clave | `agregar()`, `obtener()`, `checkout()`, `procesar_pago()` (usa transacciones) |

### 5. Filtros / Seguridad
| Filtro | Propósito |
|--------|-----------|
| `adminAuth` | Protege las rutas del grupo `/admin` (solo administradores) |

---

##  Diagrama de arquitectura

### Arquitectura general (patrón MVC de CodeIgniter 4)

```
                  ┌──────────────────────────────────────────────┐
                  │                  NAVEGADOR                    │
                  │            (Cliente / Usuario)                │
                  └──────────────────┬───────────────────────────┘
                                     │  HTTP (GET/POST)
                                     ▼
                  ┌──────────────────────────────────────────────┐
                  │                   index.php                   │
                  │                (public/index.php)             │
                  └──────────────────┬───────────────────────────┘
                                     │  Boot del framework
                                     ▼
                  ┌──────────────────────────────────────────────┐
                  │              ROUTER (app/Config/Routes.php)   │
                  └──────────────────┬───────────────────────────┘
                                     │  Despacha la solicitud
                                     ▼
                  ┌──────────────────────────────────────────────┐
                  │            CONTROLADORES (Controllers/)       │
                  │   Home │ Auth │ Admin │ Carrito               │
                  └───────┬──────────────────────┬───────────────┘
                          │                      │
                instancia/usa modelos       renderiza vistas
                          ▼                      ▼
        ┌────────────────────────────┐   ┌────────────────────────┐
        │       MODELOS (Models/)    │   │      VISTAS (Views/)   │
        │ Usuario, Producto, Auto,   │   │ index, login, registro,│
        │ Pedido, Detalle, Carrito,  │   │ admin/*, checkout...   │
        │ Rol                        │   └────────────────────────┘
        └─────────────┬──────────────┘
                      │ Consultas SQL (MySQLi)
                      ▼
        ┌────────────────────────────┐
        │   BASE DE DATOS MySQL      │
        │        ds_g1               │
        │  (7 tablas: tUsuarios,     │
        │   tRoles, tProductos,      │
        │   tAutos, tPedidos,        │
        │   tDetallePedidos,         │
        │   tCarritoCompras)         │
        └────────────────────────────┘
```

### Ciclo de una petición
1. El navegador envía una petición HTTP a `index.php`.
2. El router (`Routes.php`) resuelve la URL y la asocia a un **controlador** y método.
3. El controlador usa los **modelos** para leer/escribir en la **base de datos**.
4. El controlador pasa datos a la **vista**, que genera el HTML final.
5. La respuesta se devuelve al navegador (página o JSON).

---

##  Modelo lógico de datos

Base de datos: **ds_g1** — Motor: InnoDB, Charset: utf8mb4.

### Tablas y relaciones

```
┌──────────────┐        ┌──────────────┐
│   tRoles     │        │  tUsuarios   │
│──────────────│        │──────────────│
│ PK id_rol    │◄── 1:N ──│ PK id_usuario│
│ nombre_rol   │        │ FK id_rol    │
│ descripcion  │        │ usuario      │
└──────────────┘        │ nombre_completo │
                        │ correo_electronico│
                        │ contrasena_hash   │
                        │ telefono          │
                        │ fecha_creacion    │
                        └───────┬──────────┘
                                │ 1:N
                                │
                        ┌───────▼──────────┐
                        │   tPedidos       │
                        │──────────────────│
                        │ PK id_pedido     │
                        │ FK id_usuario    │
                        │ fecha_pedido     │
                        │ estado_pedido    │
                        │ monto_total      │
                        └───────┬──────────┘
                                │ 1:N
                        ┌───────▼──────────┐     ┌──────────────┐
                        │ tDetallePedidos  │     │  tProductos  │
                        │──────────────────│     │──────────────│
                        │ PK id_detalle    │     │ PK id_producto│
                        │ FK id_pedido     │────►│ nombre       │
                        │ FK id_producto   │──┐  │ descripcion  │
                        │ cantidad         │  │  │ precio       │
                        │ precio_unitario_ │  │  │ imagen       │
                        │   historico      │  │  │ stock        │
                        └──────────────────┘  │  │ estado_activo│
                                              │  └──────┬───────┘
        ┌──────────────┐  1:N  ┌─────────────┐  │        │ 1:N
        │  tAutos      │       │tCarritoComp.│  │        │
        │──────────────│       │─────────────│  │        │
        │ PK id_auto   │       │ PK id_carrito│  │        │
        │ modelo       │       │ FK id_usuario│──┘        │
        │ descripcion  │       │ FK id_producto│───────────┘
        │ url_imagen   │       │ cantidad     │
        └──────────────┘       │ fecha_agregado│
                               └──────────────┘
```

### Diccionario de datos

#### tRoles
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_rol | INT | PK | Identificador del rol |
| nombre_rol | VARCHAR(50) | | Nombre del rol (Administrador/Cliente) |
| descripcion | VARCHAR(255) | | Descripción del rol |

#### tUsuarios
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_usuario | INT | PK | Identificador del usuario |
| id_rol | INT | FK → tRoles.id_rol | Rol del usuario |
| usuario | VARCHAR(50) | UQ | Nombre de usuario (login) |
| nombre_completo | VARCHAR(150) | | Nombre completo |
| correo_electronico | VARCHAR(150) | UQ | Correo electrónico |
| contrasena_hash | VARCHAR(255) | | Contraseña |
| telefono | VARCHAR(20) | | Teléfono |
| fecha_creacion | DATETIME | | Fecha de alta |

#### tProductos
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_producto | INT | PK | Identificador del producto |
| nombre | VARCHAR(150) | | Nombre del producto |
| descripcion | TEXT | | Descripción |
| precio | DECIMAL(10,2) | | Precio en S/ |
| imagen | VARCHAR(255) | | Ruta de la imagen |
| stock | INT | | Unidades disponibles |
| estado_activo | TINYINT | | 1 = activo, 0 = inactivo |

#### tAutos
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_auto | INT | PK | Identificador del auto |
| modelo | VARCHAR(150) | | Modelo del auto |
| descripcion | TEXT | | Descripción/proyecto |
| url_imagen | VARCHAR(255) | | URL de la imagen |

#### tPedidos
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_pedido | INT | PK | Identificador del pedido |
| id_usuario | INT | FK → tUsuarios.id_usuario | Cliente que realiza el pedido |
| fecha_pedido | DATETIME | | Fecha del pedido |
| estado_pedido | VARCHAR(20) | | Pendiente / Pagado / Cancelado |
| monto_total | DECIMAL(10,2) | | Total del pedido |

#### tDetallePedidos
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_detalle | INT | PK | Identificador del detalle |
| id_pedido | INT | FK → tPedidos.id_pedido | Pedido al que pertenece |
| id_producto | INT | FK → tProductos.id_producto | Producto comprado |
| cantidad | INT | | Cantidad de unidades |
| precio_unitario_historico | DECIMAL(10,2) | | Precio al momento de la compra |

#### tCarritoCompras
| Campo | Tipo | Llave | Descripción |
|-------|------|-------|-------------|
| id_carrito | INT | PK | Identificador del ítem del carrito |
| id_usuario | INT | FK → tUsuarios.id_usuario | Cliente dueño del carrito |
| id_producto | INT | FK → tProductos.id_producto | Producto agregado |
| cantidad | INT | | Cantidad |
| fecha_agregado | DATETIME | | Fecha de alta en el carrito |

### Relaciones resumen (claves foráneas)
| FK | Tabla origen | Tabla destino | Cardinalidad |
|----|--------------|---------------|--------------|
| id_rol | tUsuarios | tRoles | N:1 |
| id_usuario | tPedidos | tUsuarios | N:1 |
| id_usuario | tCarritoCompras | tUsuarios | N:1 |
| id_pedido | tDetallePedidos | tPedidos | N:1 |
| id_producto | tDetallePedidos | tProductos | N:1 |
| id_producto | tCarritoCompras | tProductos | N:1 |

> El script SQL completo está en **`app/Database/ds_g1.sql`**.

---

##  Guía de instalación / puesta en marcha local

### Requisitos
- PHP ≥ 8.1 (recomendado 8.2) con extensiones `mysqli`, `mbstring`, `intl`
- MySQL / MariaDB
- Compositor (opcional para dependencias)

### Pasos

1. **Crear la base de datos** (desde una terminal en la raíz del proyecto):
   ```
   mysql -u root < app/Database/ds_g1.sql
   ```

2. **Configurar el entorno** (`app/Config/` / `.env`):
   ```
   CI_ENVIRONMENT = development
   app_baseURL    = 'http://localhost:8080/'

   database.default.hostname = localhost
   database.default.database = ds_g1
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port     = 3306
   ```

3. **Arrancar el servidor de desarrollo**:
   ```
   php spark serve --port=8080
   ```

4. **Abrir en el navegador**:
   ```
   http://localhost:8080/
   ```

---

##  Credenciales de prueba

| Rol | Usuario | Contraseña |
|-----|---------|------------|
| Administrador | `admin` | `admin123` |
| Administrador | `richard` | `123456` |
| Cliente | `cliente` | `cliente123` |
| Ventas | `heral` | `123456` |

---

##  Rutas principales

| Método | Ruta | Controlador::método |
|--------|------|---------------------|
| GET | `/` | `Home::index` |
| GET/POST | `/login` `/auth/login` | `AuthController` |
| GET/POST | `/registro` `/auth/registrar` | `AuthController` |
| GET | `/auth/logout` | `AuthController::logout` |
| GET | `/admin/dashboard` | `AdminController::dashboard` |
| GET/POST | `/admin/productos*` | `AdminController` |
| GET/POST | `/admin/usuarios*` | `AdminController` |
| GET/POST | `/carrito/*` | `CarritoController` |

---


link de el programa completo=https://drive.google.com/drive/folders/1XS4mgkrbs3DvnN-uK1rdMmz0hDTh5Q7m?usp=sharing

---

## Migraciones, datos de prueba y despliegue (Guía 9)

La estructura de la BD ahora también está versionada como **migraciones de CodeIgniter** (`app/Database/Migrations/`); `app/Database/ds_g1.sql` se conserva para reiniciar rápidamente la base de desarrollo.

```bash
composer install
cp env .env                      # Windows CMD: copy env .env  (luego editar la BD)
php spark migrate:status
php spark migrate
php spark db:seed DatosInicialesSeeder   # datos ficticios de laboratorio
php spark serve --port 8080
```

- Endpoint de salud: `GET /salud` → `{"estado":"ok"}`.
- Pruebas de humo: `scripts/smoke_test.sh` / `scripts/smoke_test.ps1`.
- Despliegue en una copia de staging: `scripts/deploy_staging.sh` / `.ps1` y `env.staging.example`.
- Informe de pruebas, defectos y despliegue: `docs/Informe_Guia9_Pruebas_Implantacion.md`.
- Preparación del entorno y línea base (Paso 0): `docs/Paso0_Preparacion_Entorno.md`.
- Verificación inicial y rama de trabajo (A1): `docs/PasoA1_Verificacion_Inicial_Rama.md`.
- Defecto INC-01 (límite de cupos): `docs/INC-01_Limite_Cupos.md`.
- Cambio SC-01 (política de cupos configurable): `docs/SC-01_Politica_Cupos_Configurable.md`.
- Las contraseñas se guardan con `password_hash()`; las cuentas antiguas en texto plano se actualizan al iniciar sesión.
