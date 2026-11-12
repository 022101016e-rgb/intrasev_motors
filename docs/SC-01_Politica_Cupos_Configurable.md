# SC-01 - Politica de cupos configurable (3 -> 4)

**Proyecto:** IntraServ Motors (CodeIgniter 4.5.1)
**Modulo:** Servicios / Solicitudes de servicio (RF-02)
**Rama:** `change/SC-01-limite-configurable`
**Rama base:** `fix/INC-01-limite-prestamos` (commit `36b55e8`)
**Fecha:** 11/11/2026
**Tipo:** Cambio de politica (no es un defecto)
**Estado:** **Implementado - suite en verde - pendiente re-prueba manual**

---

## 1. Que cambia

| | Antes (INC-01 corregido) | Despues (SC-01) |
|---|---|---|
| Regla | Maximo **3** cupos por bloque y fecha | Maximo **4** cupos por bloque y fecha |
| Donde vive el limite | `tBloquesHorarios.capacidad` (una fila por bloque) | `Config\Servicios::$maxCuposPorBloque` |
| Como se ajusta | `UPDATE` en la base de datos | Valor por defecto en el codigo o override en `.env` |
| Estado del codigo | Correcto para la regla de 3 | Correcto para la nueva regla de 4 |

**No hay defecto en SC-01.** El codigo de INC-01 ya era correcto para la regla vigente de 3. Lo que cambia es la regla del negocio: ahora se permiten 4 cupos y el maximo debe poder ajustarse sin tocar la logica.

---

## 2. Separacion respecto de INC-01

| Aspecto | INC-01 (defecto) | SC-01 (cambio de politica) |
|---|---|---|
| Regla vigente | Maximo 3 | Maximo 4 |
| Que estaba mal | Comparaba con `<=` en vez de `<` | Nada; la politica cambio |
| Sintoma | Con 3 ocupados se permitia el 4.º | Se necesita que 4 sea lo permitido |
| Solucion | Cambiar `<=` por `<` | Llevar el limite a configuracion y poner 4 |
| Rama | `fix/INC-01-limite-prestamos` | `change/SC-01-limite-configurable` |
| Commit | `36b55e8` | `change: adaptar politica SC-01` |

SC-01 se construye **sobre la rama ya corregida**. No reabre INC-01 ni modifica la comparacion `hayCupo()`; reutiliza la regla correcta y solo cambia el valor de capacidad y su origen.

---

## 3. Diseno aplicado (opcion A)

### 3.1 Configuracion centralizada

**Archivo nuevo:** `app/Config/Servicios.php`

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Servicios extends BaseConfig
{
    public int $maxCuposPorBloque = 3;
}
```

El valor por defecto versionado es **3**. CodeIgniter permite sobrescribir cualquier propiedad con una variable de entorno con el prefijo corto de la clase (`servicios.`).

**Archivo ignorado:** `.env`

```ini
#--------------------------------------------------------------------
# POLITICA DE SERVICIOS (SC-01)
#--------------------------------------------------------------------
servicios.maxCuposPorBloque = 4
```

Con ese override, la politica institucional efectiva queda en **4**.

### 3.2 Uso en el controlador

`SolicitudController::guardar()` deja de leer `bloque['capacidad']` y consulta la configuracion:

```php
use Config\Servicios;

$politicaCupos = config(Servicios::class);

if (!$disponibilidad->hayCupo($count, $politicaCupos->maxCuposPorBloque)) {
    // rechazo con el mismo mensaje de "Sin disponibilidad"
}
```

La consulta `SELECT ... FOR UPDATE` ya no trae la columna `capacidad`; el bloqueo de fila se conserva para evitar condiciones de carrera (DEF-04).

### 3.3 Uso en la vista

`SolicitudController::nuevo()` pasa `maxCuposPorBloque` a la vista y `app/Views/servicios/nuevo.php` muestra ese valor en el desplegable de bloques, en lugar de `$b['capacidad']`:

```php
'bloques'            => $bloqueModel->findAll(),
'maxCuposPorBloque'  => config(Servicios::class)->maxCuposPorBloque,
```

Asi la interfaz y la validacion muestran y aplican el mismo limite.

---

## 4. Verificacion de la configuracion

Comando:

```bash
php spark config:check Servicios
```

Salida real (con `.env` aplicado):

```text
CodeIgniter v4.5.1 Command Line Tool - Server Time: 2026-11-11 22:17:52 UTC-05:00

Config\Servicios#35 (1) (
    public 'maxCuposPorBloque' -> integer 4
)

Config Caching: Disabled
```

Esto confirma que la clase lee el valor **4** desde el override de `.env`.

---

## 5. Prueba automatizada de la nueva politica

**Archivo nuevo:** `tests/unit/PoliticaCuposSc01Test.php`

Valida la regla `hayCupo()` con capacidad 4 y seis casos frontera:

| Ocupados | Esperado |
|---|---|
| 0, 1, 2, 3 | Se acepta |
| 4, 5 | Se rechaza |

Comando:

```bash
vendor\bin\phpunit
```

Salida real de la suite completa:

```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: D:\C BODRIO\intrasev\intrasev-motors-main\phpunit.xml.dist

..............                                                    14 / 14 (100%)

Time: 00:00.055, Memory: 14.00 MB

OK (14 tests, 17 assertions)
```

La prueba anterior de INC-01 (`DisponibilidadServiceTest`, capacidad 3) sigue en verde: la correccion del defecto no se altero.

> La prueba usa capacidad 4 de forma explicita y no depende de `.env`. La lectura del override se verifica aparte con `php spark config:check Servicios` (seccion 4). Asi la suite es estable aunque el archivo `.env` no exista en otro entorno.

---

## 6. Replica del cambio

1. Crear `app/Config/Servicios.php` con `maxCuposPorBloque = 3` (valor por defecto versionado).
2. Agregar en `.env` el override:
   ```ini
   servicios.maxCuposPorBloque = 4
   ```
   En staging/produccion se usa el mismo par clave-valor en el `.env` del entorno.
3. En `SolicitudController::guardar()`, usar `config(Servicios::class)->maxCuposPorBloque` en lugar de `bloque['capacidad']`.
4. Pasar el valor a la vista `servicios/nuevo.php` para mostrarlo.
5. Ejecutar `php spark config:check Servicios` y `vendor\bin\phpunit`.

---

## 7. Nota sobre versionado de `.env`

`.env` esta en `.gitignore`, por lo que el override `= 4` **no se versiona**. Para que el cambio sea reproducible:

- `app/Config/Servicios.php` deja el valor por defecto **3** en Git.
- `env` (plantilla local) y `env.staging.example` incluyen la clave comentada con el ejemplo `= 4`, para que quien despliegue sepa que debe definirla.

Si se prefiere que el valor 4 quede en Git sin depender de `.env`, la alternativa es dejar `maxCuposPorBloque = 4` como valor por defecto en `app/Config/Servicios.php` y usar `.env` solo para ajustes por entorno. En este trabajo se eligio la opcion A indicada: **3 por defecto en codigo y 4 por override**.

---

## 8. Rollback

Para volver a la politica de 3 cupos:

1. Quitar o comentar en `.env`:
   ```ini
   # servicios.maxCuposPorBloque = 4
   ```
2. Reiniciar el servidor (o limpiar cache de configuracion si estuviera activa).
3. Verificar con `php spark config:check Servicios` que el valor vuelve a **3**.

No hace falta revertir el commit: el valor por defecto del codigo ya es 3.

---

## 9. Criterio de aceptacion

| # | Criterio | Estado |
|---|---|---|
| 1 | La politica se define en `Config\Servicios` | Hecho |
| 2 | `.env` puede sobrescribir el valor a 4 | Hecho - verificado con `config:check` |
| 3 | El controlador ya no usa `bloque['capacidad']` | Hecho |
| 4 | La vista muestra el limite configurado | Hecho |
| 5 | `hayCupo()` acepta hasta 3 ocupados y rechaza desde 4 (capacidad 4) | Hecho - prueba automatizada |
| 6 | Suite completa en verde | Hecho - 14 tests, 17 assertions |
| 7 | Re-prueba manual: registrar 4 solicitudes y rechazar la 5.ª | **Pendiente** |

---

## 10. Archivos relacionados

| Archivo | Cambio |
|---|---|
| `app/Config/Servicios.php` | Nuevo: politica institucional configurable |
| `app/Controllers/SolicitudController.php` | Usa `config(Servicios::class)`; pasa el maximo a la vista |
| `app/Views/servicios/nuevo.php` | Muestra `$maxCuposPorBloque` |
| `tests/unit/PoliticaCuposSc01Test.php` | Nuevo: prueba de la politica de 4 |
| `env` | Plantilla con la clave comentada de ejemplo |
| `env.staging.example` | Clave `servicios.maxCuposPorBloque = 4` para staging |
| `docs/SC-01_Politica_Cupos_Configurable.md` | Este documento |

---

## 11. Historial

| Fecha | Commit | Accion | Resultado |
|---|---|---|---|
| 11/11/2026 | `change: adaptar politica SC-01` | Config centralizada, override 4, prueba de la politica | **OK (14 tests, 17 assertions)** |
| Pendiente | - | Re-prueba manual en navegador (criterio 9.7) | Pendiente |
