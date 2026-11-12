-- NOTA (Guía 9): el esquema vigente se versiona en app/Database/Migrations y los datos
-- en app/Database/Seeds/DatosInicialesSeeder.php. Este script sigue sirviendo para resetear
-- la BD de DESARROLLO, pero guarda contraseñas de ejemplo en texto plano (el login las
-- convierte a hash en el primer acceso). No lo use en staging.
-- ============================================================
-- INTRASEV MOTORS - Esquema y datos de la base de datos (ds_g1)
-- Motor compatible: MySQL / MariaDB (XAMPP)
-- Creado con CodeIgniter 4 (PHP 8.2, MySQLi)
--
-- Cómo usarlo en local (XAMPP):
--   mysql -u root < app/Database/ds_g1.sql
--
-- NOTA: Este script re-crea las tablas (DROP + CREATE) y
--       reinserta los datos de ejemplo. Perfecto para resetear.
-- ============================================================

CREATE DATABASE IF NOT EXISTS ds_g1
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE ds_g1;

-- ============================================================
-- LIMPIEZA (orden por dependencias)
-- ============================================================
DROP TABLE IF EXISTS tSolicitudesServicio;
DROP TABLE IF EXISTS tVehiculos;
DROP TABLE IF EXISTS tBloquesHorarios;
DROP TABLE IF EXISTS tClientes;
DROP TABLE IF EXISTS tDetallePedidos;
DROP TABLE IF EXISTS tCarritoCompras;
DROP TABLE IF EXISTS tPedidos;
DROP TABLE IF EXISTS tUsuarios;
DROP TABLE IF EXISTS tProductos;
DROP TABLE IF EXISTS tAutos;
DROP TABLE IF EXISTS tRoles;

-- ============================================================
-- TABLA: tRoles
-- ============================================================
CREATE TABLE tRoles (
    id_rol      INT AUTO_INCREMENT NOT NULL,
    nombre_rol  VARCHAR(50)  NOT NULL,
    descripcion VARCHAR(255) NULL,
    PRIMARY KEY (id_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tUsuarios
--   Relación: tUsuarios.id_rol -> tRoles.id_rol (N:1)
-- ============================================================
CREATE TABLE tUsuarios (
    id_usuario         INT AUTO_INCREMENT NOT NULL,
    id_rol             INT NOT NULL,
    usuario            VARCHAR(50)  NOT NULL,
    nombre_completo    VARCHAR(150) NOT NULL,
    correo_electronico VARCHAR(150) NOT NULL,
    contrasena_hash    VARCHAR(255) NOT NULL,
    telefono           VARCHAR(20)  NULL,
    fecha_creacion     DATETIME     NULL,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_usuario (usuario),
    UNIQUE KEY uq_correo (correo_electronico),
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (id_rol) REFERENCES tRoles (id_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tClientes  (módulo de Servicios)
--   Relación: tClientes.id_usuario -> tUsuarios.id_usuario (N:1)
--   estado: 'Activo' | 'Inactivo' | 'Deudor'
--   deudas: monto pendiente de pago (0.00 = sin deuda)
-- ============================================================
CREATE TABLE tClientes (
    id_cliente INT AUTO_INCREMENT NOT NULL,
    id_usuario INT NOT NULL,
    estado     VARCHAR(20) NOT NULL DEFAULT 'Activo',
    deudas     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_cliente),
    CONSTRAINT fk_clientes_usuario FOREIGN KEY (id_usuario) REFERENCES tUsuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tVehiculos  (módulo de Servicios)
--   Relación: tVehiculos.id_cliente -> tClientes.id_cliente (N:1)
-- ============================================================
CREATE TABLE tVehiculos (
    id_vehiculo INT AUTO_INCREMENT NOT NULL,
    id_cliente  INT NOT NULL,
    placa       VARCHAR(15) NOT NULL,
    marca       VARCHAR(60) NOT NULL,
    modelo      VARCHAR(60) NOT NULL,
    anio        INT NULL,
    PRIMARY KEY (id_vehiculo),
    UNIQUE KEY uq_placa (placa),
    CONSTRAINT fk_vehiculos_cliente FOREIGN KEY (id_cliente) REFERENCES tClientes (id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tBloquesHorarios  (módulo de Servicios)
--   Representa el horario del taller. capacidad = máx. solicitudes
--   aceptadas por bloque en una misma fecha (por defecto 3).
-- ============================================================
CREATE TABLE tBloquesHorarios (
    id_bloque   INT AUTO_INCREMENT NOT NULL,
    nombre      VARCHAR(50) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin    TIME NOT NULL,
    capacidad   INT NOT NULL DEFAULT 3,
    PRIMARY KEY (id_bloque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tSolicitudesServicio  (módulo de Servicios)
--   Relaciones:
--     tSolicitudesServicio.id_cliente  -> tClientes.id_cliente     (N:1)
--     tSolicitudesServicio.id_vehiculo -> tVehiculos.id_vehiculo   (N:1)
--     tSolicitudesServicio.id_bloque   -> tBloquesHorarios.id_bloque (N:1)
--   estado por defecto: 'Pendiente' (RF-01)
-- ============================================================
CREATE TABLE tSolicitudesServicio (
    id_solicitud   INT AUTO_INCREMENT NOT NULL,
    codigo         VARCHAR(20) NOT NULL,
    id_cliente     INT NOT NULL,
    id_vehiculo    INT NOT NULL,
    id_bloque      INT NOT NULL,
    fecha          DATE NOT NULL,
    hora           TIME NOT NULL,
    estado         VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    detalle        TEXT NULL,
    fecha_registro DATETIME NULL,
    PRIMARY KEY (id_solicitud),
    UNIQUE KEY uq_codigo (codigo),
    CONSTRAINT fk_solicitudes_cliente  FOREIGN KEY (id_cliente)  REFERENCES tClientes (id_cliente),
    CONSTRAINT fk_solicitudes_vehiculo FOREIGN KEY (id_vehiculo) REFERENCES tVehiculos (id_vehiculo),
    CONSTRAINT fk_solicitudes_bloque   FOREIGN KEY (id_bloque)   REFERENCES tBloquesHorarios (id_bloque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tProductos
-- ============================================================
CREATE TABLE tProductos (
    id_producto   INT AUTO_INCREMENT NOT NULL,
    nombre        VARCHAR(150) NOT NULL,
    descripcion   TEXT NULL,
    precio        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    imagen        VARCHAR(255) NULL,
    stock         INT NOT NULL DEFAULT 0,
    estado_activo TINYINT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tAutos
-- ============================================================
CREATE TABLE tAutos (
    id_auto     INT AUTO_INCREMENT NOT NULL,
    modelo      VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    url_imagen  VARCHAR(255) NULL,
    PRIMARY KEY (id_auto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tPedidos
--   Relación: tPedidos.id_usuario -> tUsuarios.id_usuario (N:1)
-- ============================================================
CREATE TABLE tPedidos (
    id_pedido     INT AUTO_INCREMENT NOT NULL,
    id_usuario    INT NOT NULL,
    fecha_pedido  DATETIME NOT NULL,
    estado_pedido VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    monto_total   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_pedido),
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (id_usuario) REFERENCES tUsuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tDetallePedidos
--   Relaciones:
--     tDetallePedidos.id_pedido   -> tPedidos.id_pedido   (N:1)
--     tDetallePedidos.id_producto -> tProductos.id_producto (N:1)
-- ============================================================
CREATE TABLE tDetallePedidos (
    id_detalle               INT AUTO_INCREMENT NOT NULL,
    id_pedido                INT NOT NULL,
    id_producto              INT NOT NULL,
    cantidad                 INT NOT NULL DEFAULT 1,
    precio_unitario_historico DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_detalle),
    CONSTRAINT fk_detalle_pedido   FOREIGN KEY (id_pedido)   REFERENCES tPedidos (id_pedido),
    CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto) REFERENCES tProductos (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: tCarritoCompras
--   Relaciones:
--     tCarritoCompras.id_usuario  -> tUsuarios.id_usuario   (N:1)
--     tCarritoCompras.id_producto -> tProductos.id_producto (N:1)
-- ============================================================
CREATE TABLE tCarritoCompras (
    id_carrito     INT AUTO_INCREMENT NOT NULL,
    id_usuario     INT NOT NULL,
    id_producto    INT NOT NULL,
    cantidad       INT NOT NULL DEFAULT 1,
    fecha_agregado DATETIME NULL,
    PRIMARY KEY (id_carrito),
    CONSTRAINT fk_carrito_usuario  FOREIGN KEY (id_usuario)  REFERENCES tUsuarios (id_usuario),
    CONSTRAINT fk_carrito_producto FOREIGN KEY (id_producto) REFERENCES tProductos (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS DE EJEMPLO (SEED)
-- ============================================================
INSERT INTO tRoles (id_rol, nombre_rol, descripcion) VALUES
(1, 'Administrador', 'Acceso total al panel de administración'),
(2, 'Cliente',       'Puede comprar en la tienda'),
(3, 'Ventas',        'Usuario de ventas sin acceso al panel de administración');

-- Admin de ejemplo -> login: admin / admin123
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(1, 'admin', 'Administrador Principal', 'admin@intrasev.com', 'admin123', '999888777', NOW());

-- Cliente de ejemplo -> login: cliente / cliente123
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(2, 'cliente', 'Cliente Demo', 'cliente@intrasev.com', 'cliente123', '987654321', NOW());

-- Admin adicional -> login: richard / 123456
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(1, 'richard', 'Richard', 'richard@intrasev.com', '123456', '999000111', NOW());

-- Usuario de Ventas -> login: heral / 123456 (sin permisos de admin)
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(3, 'heral', 'Heral Ose', 'heral@intrasev.com', '123456', '888777666', NOW());

INSERT INTO tProductos (nombre, descripcion, precio, imagen, stock, estado_activo) VALUES
('Kit de Frenos Cerámicos', 'Kit de pastillas y discos de competición.', 450.00, NULL, 25, 1),
('Intercooler Frontal', 'Intercooler de mayor capacidad para turbo.', 680.00, NULL, 12, 1),
('Escape Deportivo Acero', 'Sistema de escape completo en acero inoxidable.', 890.00, NULL, 8, 1),
('Filtro de Aire Deportivo', 'Filtro de alto flujo reutilizable.', 120.00, NULL, 40, 1);

INSERT INTO tAutos (modelo, descripcion, url_imagen) VALUES
('Honda Civic 1998', 'Proyecto JDM con motor VTEC.', NULL),
('VW Golf MK3', 'Reprogramación Stage1 y suspensión coilover.', NULL),
('Subaru Impreza 98', 'AWD reforzado con turbo y rally setup.', NULL);

-- ============================================================
-- DATOS DE PRUEBA: MÓDULO DE SERVICIOS
-- Clientes en diferentes estados (Activo, Deudor, Inactivo)
-- ============================================================

-- Cliente deudor -> login: deudor / deudor123  (Activo con deuda)
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(2, 'deudor', 'Cliente deudor', 'deudor@intrasev.com', 'deudor123', '900111222', NOW());

-- Cliente inactivo -> login: inactivo / inactivo123 (Inactivo sin deuda)
INSERT INTO tUsuarios (id_rol, usuario, nombre_completo, correo_electronico, contrasena_hash, telefono, fecha_creacion) VALUES
(2, 'inactivo', 'Cliente inactivo', 'inactivo@intrasev.com', 'inactivo123', '911222333', NOW());

INSERT INTO tClientes (id_usuario, estado, deudas) VALUES
(2, 'Activo',   0.00),    -- cliente (login: cliente / cliente123)
(5, 'Activo',   500.00),  -- deudor  (login: deudor / deudor123)
(6, 'Inactivo', 0.00);    -- inactivo (login: inactivo / inactivo123)

INSERT INTO tVehiculos (id_cliente, placa, marca, modelo, anio) VALUES
(1, 'ABC-123', 'Toyota', 'Corolla',         2015),
(1, 'XYZ-789', 'VW',     'Golf MK3',        1998),
(2, 'DEF-456', 'Kia',    'Picanto',         2018),
(3, 'GHI-000', 'Honda',  'Civic',           2000);

INSERT INTO tBloquesHorarios (nombre, hora_inicio, hora_fin, capacidad) VALUES
('Mañana A', '08:00:00', '10:00:00', 3),
('Mañana B', '10:00:00', '12:00:00', 3),
('Tarde A',  '14:00:00', '16:00:00', 3),
('Tarde B',  '16:00:00', '18:00:00', 3);

-- Solicitudes de ejemplo (RF-04: consultar por código o placa)
INSERT INTO tSolicitudesServicio (codigo, id_cliente, id_vehiculo, id_bloque, fecha, hora, estado, detalle, fecha_registro) VALUES
('SOL-0001', 1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', 'Pendiente', 'Cambio de aceite y filtros.', NOW()),
('SOL-0002', 1, 2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 'Pendiente', 'Reprogramación Stage 1.', NOW());
