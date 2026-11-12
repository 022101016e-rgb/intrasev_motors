# INC-01 - El limite de cupos permite una solicitud de mas

**Proyecto:** IntraServ Motors (CodeIgniter 4.5.1)
**Modulo:** Servicios / Solicitudes de servicio (RF-02)
**Rama:** `fix/INC-01-limite-prestamos`
**Fecha de registro:** 11/11/2026
**Estado:** **Corregido - listo para re-prueba (suite en verde)**
**Severidad:** Alta
**Prioridad:** Alta
**Responsable:** [completar]

---

## 1. Regla vigente

La capacidad de cada bloque horario se configura en `tBloquesHorarios.capacidad`. Para el entorno de laboratorio el valor es **3 cupos por bloque y fecha**.

> En el momento de INC-01 la capacidad se leia de `tBloquesHorarios.capacidad`. El cambio de politica SC-01, en su propia rama, mueve ese maximo a `Config\Servicios` y lo deja en 4. INC-01 se limita a corregir la comparacion para cumplir la regla vigente de 3.

| Solicitudes ya registradas | Resultado esperado |
|---|---|
| 0, 1 o 2 | Se acepta una nueva solicitud |
| 3 | Se rechaza: *Sin disponibilidad para el bloque...* |
| 4 o mas | Se rechaza |

El registro de la solicitud esta en `SolicitudController::guardar()` y la regla de cupo en `App\Libraries\DisponibilidadService`.

---

## 2. Descripcion del defecto

Con **3 cupos ya ocupados** en un bloque y fecha, el sistema **permite registrar una cuarta solicitud**. El bloque deberia estar lleno y el alta deberia rechazarse con el mensaje de *Sin disponibilidad*.

**Sintoma observable:** el contador del bloque llega a 4 cuando la capacidad configurada es 3.

---

## 3. Causa raiz

`DisponibilidadService::hayCupo()` compara con `<=` en lugar de `<`:

```php
public function hayCupo(int $ocupados, int $capacidad): bool
{
    return $ocupados <= $capacidad;
}
```

Con `$ocupados = 3` y `$capacidad = 3`, el metodo devuelve `true` (hay cupo), cuando deberia devolver `false` (bloque lleno).

**Correccion esperada:**

```php
return $ocupados < $capacidad;
```

### 3.1 Cambio de apoyo para poder probar la regla

Antes de este incidente la comparacion estaba embebida en `SolicitudController::guardar()`:

```php
if ($count >= (int) $bloque['capacidad']) { /* rechazar */ }
```

Para poder reproducir y verificar el defecto con una prueba automatizada, en el commit de reproduccion se extrajo la regla a `App\Libraries\DisponibilidadService`. El controlador ahora consulta `hayCupo()` y conserva el mismo mensaje de rechazo. Este cambio **no altera la regla vigente**; solo la aisla para probarla.

---

## 4. Reproduccion automatizada

**Archivo:** `tests/unit/DisponibilidadServiceTest.php`
**Caso que falla:** `tres cupos ocupados: lleno` (`ocupados = 3`, `capacidad = 3`, esperado `false`).

Comando:

```bash
vendor\bin\phpunit
```

Resultado en el commit "antes":

```text
...F....                                                            8 / 8 (100%)

There was 1 failure:

1) Tests\Unit\DisponibilidadServiceTest::testLaReglaDeCupoRespetaLaCapacidad
   with data set "tres cupos ocupados: lleno" (3, 3, false)
Failed asserting that true is identical to false.

FAILURES!
Tests: 8, Assertions: 11, Failures: 1.
```

Este resultado es el **estado rojo esperado**: la prueba representa la regla de negocio y falla porque el codigo permite el cupo numero 4.

### 4.1 Resultado despues de la correccion

Comando:

```bash
vendor\bin\phpunit
```

Resultado en el commit "despues":

```text
........                                                            8 / 8 (100%)

Time: 00:00.048, Memory: 14.00 MB

OK (8 tests, 11 assertions)
```

El caso de 3 cupos ocupados ahora devuelve `false` y la suite completa queda en verde.

---

## 5. Impacto

- Se puede superar la capacidad del bloque (sobreventa de cupos).
- Se incumple RF-02 (disponibilidad por bloque y fecha).
- No hay perdida ni corrupcion de datos.
- No es un problema de seguridad ni de permisos.

---

## 6. Criterio de aceptacion

1. `hayCupo(0..2, 3)` devuelve `true`.
2. `hayCupo(3, 3)` devuelve `false`.
3. `hayCupo(4, 3)` devuelve `false`.
4. La suite completa queda en verde.
5. Re-prueba funcional: con 3 solicitudes registradas en un bloque/fecha, el cuarto intento se rechaza con el mensaje de *Sin disponibilidad*.

Estado de la verificacion:

- [x] Puntos 1 a 4: verificados con PHPUnit en el commit "despues".
- [ ] Punto 5: pendiente de re-prueba manual en navegador.

---

## 7. Plan de correccion

| Paso | Commit | Accion | Estado |
|---|---|---|---|
| A (antes) | `test: reproducir INC-01` | Se agrega la prueba, se introduce el defecto y se documenta | **Hecho** (rojo) |
| B (despues) | `fix: corregir limite INC-01` | Cambiar `<=` por `<`, ejecutar la suite y actualizar este documento | **Hecho** (verde) |
| C (cambio) | `change: adaptar politica SC-01` | En otra rama, mover el limite a configuracion y poner 4 | **Hecho** en `change/SC-01-limite-configurable` (ver `docs/SC-01_Politica_Cupos_Configurable.md`) |

La re-prueba del punto 6.5 se registrara en este mismo documento al cerrar INC-01. No se marca como cerrado hasta repetir el escenario.

---

## 8. Separacion entre INC-01 y SC-01

| Aspecto | INC-01 (defecto) | SC-01 (cambio de politica) |
|---|---|---|
| Regla vigente | Maximo 3 cupos por bloque/fecha | Maximo 4 cupos por bloque/fecha |
| Que esta mal | El codigo compara con `<=` en vez de `<` | Nada; la politica cambio |
| Sintoma | Con 3 ocupados se permite el 4.º | Se necesita que 4 sea lo permitido |
| Solucion | Cambiar `<=` por `<` | Llevar el limite a configuracion y poner 4 |
| Rama | `fix/INC-01-limite-prestamos` | `change/SC-01-...` |

**Importante:** no se mezclan. INC-01 corrige el codigo para cumplir la regla actual (3). SC-01 se trabaja despues, sobre la rama ya corregida, y cambia la regla a 4 mediante configuracion.

---

## 9. Archivos relacionados

| Archivo | Cambio |
|---|---|
| `app/Libraries/DisponibilidadService.php` | Nuevo: regla de cupo (con el defecto en el commit A) |
| `app/Controllers/SolicitudController.php` | Usa `DisponibilidadService::hayCupo()` |
| `tests/unit/DisponibilidadServiceTest.php` | Nuevo: prueba de la regla con capacidad 3 |
| `docs/INC-01_Limite_Cupos.md` | Este documento |

---

## 10. Historial

| Fecha | Commit | Accion | Resultado |
|---|---|---|---|
| 11/11/2026 | `b70f406` - `test: reproducir INC-01` | Reproduccion con prueba automatizada | 8 tests, 11 aserciones, **1 fallo** (rojo) |
| 11/11/2026 | `fix: corregir limite INC-01` | Correccion `<=` -> `<` | **OK (8 tests, 11 assertions)** |
| Pendiente | - | Re-prueba manual en navegador (punto 6.5) | Pendiente |
