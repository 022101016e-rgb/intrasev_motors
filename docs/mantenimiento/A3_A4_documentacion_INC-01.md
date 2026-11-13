# A3-A4 · Documentación de la corrección INC-01

**Proyecto:** IntraServ Motors (CodeIgniter 4.5.1)
**Módulo:** Préstamos (`app/Services/PrestamoService.php`)
**Incidencia:** INC-01 — el límite de préstamos permite uno de más
**Rama:** `fix/INC-01-limite-prestamos`
**Commits:** `fa649e3` (prueba roja) → `357093b` (corrección) → `8cea74f` (evidencias) → `8f3a9e8` (integración a `master`)
**Fecha de revisión:** 12/11/2026

---

## 1. Objetivo de esta revisión

Verificar que las partes **A3** y **A4** del mantenimiento se hicieron realmente y dejar la documentación trazable:

- **A3:** escribir las pruebas PHPUnit; la prueba de “rechaza al alcanzar el límite” debe fallar al inicio.
- **A4:** documentar la causa raíz, cambiar `<=` por `<`, volver a ejecutar las pruebas y hacer el commit.

---

## 2. Regla vigente y criterio

La regla anterior permite **máximo 3 préstamos activos**:

| Préstamos activos (`actuales`) | Resultado esperado |
|---|---|
| 0, 1 o 2 | Se permite registrar |
| 3 | Se rechaza: ya alcanzó el máximo |
| 4 o más | Se rechaza |

`$actuales` es la cantidad **antes** de registrar el nuevo préstamo. Por eso la comparación correcta es `$actuales < $this->maximo`.

---

## 3. A3 — Pruebas escritas antes de corregir

**Archivo:** `tests/unit/PrestamoServiceMantenimientoTest.php`
**Commit:** `fa649e3 test: reproducir defecto INC-01 (falla esperada)`

La versión del commit `fa649e3` contiene tres pruebas:

| Prueba | Entrada | Esperado |
|---|---|---|
| `testPermiteAntesDelLimite` | `PrestamoService(3)->puedeRegistrar(2)` | `true` |
| `testRechazaAlAlcanzarElLimite` | `PrestamoService(3)->puedeRegistrar(3)` | `false` |
| `testRechazaPorEncimaDelLimite` | `PrestamoService(3)->puedeRegistrar(4)` | `false` |

En ese commit el servicio conserva el defecto:

```php
// DEFECTO DOCENTE INC-01: se analizara en A4.
return $actuales <= $this->maximo;
```

### Verificación real del estado rojo

Comando ejecutado sobre `fa649e3`:

```powershell
git checkout fa649e3
vendor\bin\phpunit --filter PrestamoServiceMantenimientoTest
```

Salida obtenida:

```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: D:\C BODRIO\intrasev\intrasev-motors-INC01\intrasev-motors-main\phpunit.xml.dist

.F.                                                                 3 / 3 (100%)

There was 1 failure:

1) Tests\Unit\PrestamoServiceMantenimientoTest::testRechazaAlAlcanzarElLimite
Failed asserting that true is false.

D:\C BODRIO\intrasev\intrasev-motors-INC01\intrasev-motors-main\tests\unit\PrestamoServiceMantenimientoTest.php:29

FAILURES!
Tests: 3, Assertions: 3, Failures: 1.
```

**Resultado:** A3 cumple. Falla exactamente la prueba de frontera (`actuales = 3`), mientras las de 2 y 4 pasan. Eso aísla el defecto en la regla del servicio.

---

## 4. A4 — Causa raíz, corrección y verificación

### 4.1 Causa raíz

`PrestamoService::puedeRegistrar()` compara con `<=`:

```php
return $actuales <= $this->maximo;
```

Con `$actuales = 3` y `$maximo = 3` devuelve `true`, cuando el bloque ya está lleno. El operador incluye el valor máximo en la zona permitida, pero la cantidad se evalúa antes de registrar el nuevo préstamo.

**Corrección mínima:** cambiar el operador por `<`.

### 4.2 Cambio aplicado

**Commit:** `357093b fix: corregir limite de prestamos INC-01`
**Archivo:** `app/Services/PrestamoService.php`

```diff
-        // DEFECTO DOCENTE INC-01: se analizara en A4.
-        return $actuales <= $this->maximo;
+        return $actuales < $this->maximo;
```

No se modifican la firma del método, las excepciones por entradas inválidas ni el constructor. El cambio es de una sola línea efectiva.

### 4.3 Verificación real del estado verde

Comando ejecutado sobre `357093b`:

```powershell
git checkout 357093b
vendor\bin\phpunit --filter PrestamoServiceMantenimientoTest
```

Salida obtenida:

```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: D:\C BODRIO\intrasev\intrasev-motors-INC01\intrasev-motors-main\phpunit.xml.dist

...                                                                 3 / 3 (100%)

OK (3 tests, 3 assertions)
```

**Resultado:** A4 cumple. La misma suite que fallaba ahora pasa completa.

### 4.4 Documentación de la causa raíz

El análisis quedó en `docs/mantenimiento/F17_causa_raiz.md` y su versión PDF `docs/mantenimiento/F17_causa_raiz.pdf`, incluyendo:

- síntoma reproducido (`/mantenimiento/evaluar/3` devolvía `puede_registrar: true`);
- hipótesis y componente examinado;
- prueba que descartó otras causas (servicio aislado, sin HTTP ni base de datos);
- causa técnica y los cinco porqués;
- qué se cambia y qué se preserva.

---

## 5. Trazabilidad

| Paso | Commit | Contenido | Estado |
|---|---|---|---|
| Línea base | `0789576` | Proyecto estable antes del mantenimiento | OK |
| A3 (rojo) | `fa649e3` | Pruebas + servicio con `<=`; falla 1 de 3 | Verificado |
| A4 (verde) | `357093b` | `<=` → `<`; pasan 3 de 3 | Verificado |
| Evidencias | `8cea74f` | Capturas E1-E3 | Presente |
| Integración | `8f3a9e8` | Merge de INC-01 a `master` | Presente |
| Estado final | `ac64f7d` | `master` con INC-01 y SC-01; suite completa 6/6 | Verificado |

### Evidencias asociadas

| Evidencia | Contenido |
|---|---|
| `E1_antes.png` / `.txt` | Endpoint con 3 activos devolviendo `puede_registrar: true` |
| `E2_test_fallido.png` / `.txt` | `.F.` y `Failures: 1` antes de corregir |
| `E3_diff_correccion.txt` | Diff de `<=` a `<` |
| `E3_test_aprobado.png` / `.txt` | `OK (3 tests, 3 assertions)` después de corregir |
| `E3_endpoint_corregido.png` | Endpoint con 3 activos devolviendo `false` |

---

## 6. Suite completa en el estado final

Comando ejecutado sobre `master` (`ac64f7d`):

```powershell
vendor\bin\phpunit
```

Salida obtenida:

```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: D:\C BODRIO\intrasev\intrasev-motors-INC01\intrasev-motors-main\phpunit.xml.dist

......                                                              6 / 6 (100%)

OK (6 tests, 6 assertions)
```

Incluye las 3 pruebas de INC-01 y las 3 de la política SC-01. No hay regresiones.

---

## 7. Observaciones de la revisión

1. **A3 y A4 están correctamente ejecutados.** El rojo y el verde se reprodujeron de forma independiente sobre los commits `fa649e3` y `357093b`.
2. **La corrección es mínima y preserva el contrato.** Solo cambia el operador; las pruebas de 2 y 4 siguen pasando.
3. **Versión de PHP unificada.** `E2_test_fallido.txt` registra PHP **8.3.6**; `F16_reproduccion.pdf`, `F19_pruebas.pdf` y `F21_cierre.pdf` indicaban PHP **8.3.33**, que no corresponde a una versión real. Se corrigieron los tres PDF a **8.3.6**, la versión de la ejecución documentada en E2. Esta revisión se re-ejecutó en este equipo con PHP **8.2.12** y el resultado rojo/verde fue el mismo, por lo que la conclusión no depende de la versión.
4. **F17 en Markdown no incluía el bloque “Cambio aplicado”** que sí aparece en el PDF. Se agregó en esta revisión para que ambas versiones coincidan.
5. Las fichas F15-F21 están en PDF; solo F17 tenía fuente Markdown. Este documento deja la trazabilidad de A3/A4 en un formato versionable.

### Correcciones aplicadas en esta revisión

| Archivo | Corrección |
|---|---|
| `F16_reproduccion.pdf` | PHP `8.3.33` → `8.3.6` |
| `F19_pruebas.pdf` | PHP `8.3.33` → `8.3.6` |
| `F21_cierre.pdf` | PHP `8.3.33` → `8.3.6` |
| `F17_causa_raiz.md` | Se agregó la sección “Cambio aplicado” y las evidencias E2/E3 |

---

## 8. Conclusión

| Criterio | Resultado |
|---|---|
| La prueba de frontera falla antes de corregir | **Sí** — `testRechazaAlAlcanzarElLimite`, `.F.`, 1 fallo |
| La causa raíz está documentada | **Sí** — `F17_causa_raiz.md` / `.pdf` |
| La corrección es `<=` → `<` | **Sí** — commit `357093b` |
| La prueba pasa después de corregir | **Sí** — `OK (3 tests, 3 assertions)` |
| Hay commit de la corrección | **Sí** — `357093b`, integrado en `8f3a9e8` |
| Suite completa sin regresiones | **Sí** — `OK (6 tests, 6 assertions)` en `ac64f7d` |

**A3 y A4 se consideran correctamente completados.**

---

## 9. Publicación en GitHub

| Dato | Valor |
|---|---|
| Repositorio | `https://github.com/022101016e-rgb/intrasev_motors` |
| Rama | `guia11-mantenimiento` |
| Commit de documentación | `5b5b57e` |
| Contenido | Fichas F15-F21, evidencias E1-E5, revisión A3/A4 y F17 actualizado |

El repositorio principal ya tenía una rama `master` y las ramas `fix/INC-01-limite-prestamos` y `change/SC-01-limite-configurable` del proyecto de servicios. Para no sobrescribir ese trabajo, la historia del proyecto INC-01 se publicó en la rama **`guia11-mantenimiento`**. Esa rama contiene los commits `fa649e3`, `357093b`, `8cea74f`, `8f3a9e8`, `a823c57`, `a962280`, `e53256e` y `ac64f7d`, además de la documentación.
