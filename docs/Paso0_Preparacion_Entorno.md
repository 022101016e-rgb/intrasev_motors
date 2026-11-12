# Paso 0 - Preparación del entorno

**Proyecto:** IntraServ Motors (CodeIgniter 4.5.1)
**Fecha de ejecución:** 11/11/2026
**Rama base:** `master`
**Commit base:** `8508899`
**Responsable:** [completar]

> **Alcance de este documento.** Registra únicamente lo que se ejecutó y se comprobó durante el Paso 0. No se marcan como aprobadas pruebas que no se hayan realizado. El detalle de lo verificado y lo no verificado está en la sección 9.

---

## 1. Objetivo

Dejar el proyecto listo para trabajar con ramas y pruebas, comprobando los cuatro requisitos del Paso 0:

1. Proyecto CodeIgniter 4 que se ejecute.
2. Rama base estable.
3. Suite de pruebas previa.
4. Acceso a Git y árbol de trabajo limpio.

---

## 2. Resumen de resultados

| # | Comprobación | Resultado | Observación |
|---|---|---|---|
| 1 | Herramientas PHP, Composer y Git | **OK** | Versiones disponibles en la sección 3 |
| 2 | `git status` limpio | **Ajustado** | El repo no tenía commits y había 766 archivos preparados; se creó el commit inicial |
| 3 | `php spark serve` y carga en `http://localhost:8080` | **OK** | `/` respondió 200 y `/salud` respondió `{"estado":"ok"}` |
| 4 | `vendor/bin/phpunit` | **Ajustado** | PHPUnit no estaba instalado ni configurado; se instaló y se creó una suite mínima. Resultado final: **OK (3 tests, 6 assertions)** |

**Conclusión:** según las comprobaciones ejecutadas, el entorno del Paso 0 queda operativo: herramientas disponibles, rama base limpia, aplicación respondiendo por HTTP y PHPUnit ejecutando la suite en verde. Esto **no** equivale a validar funcionalmente todos los módulos del sistema (ver sección 9).

---

## 3. Herramientas verificadas

| Herramienta | Versión | Comando |
|---|---|---|
| PHP | 8.2.12 (cli, ZTS, Visual C++ 2019 x64) | `php --version` |
| Composer | 2.9.8 | `composer --version` |
| Git | 2.54.0.windows.1 | `git --version` |
| PHPUnit | 11.5.56 | `vendor\bin\phpunit --version` |

Extensiones de PHP relevantes comprobadas con `php -m`: `intl`, `mbstring`, `mysqli`, `dom`, `libxml`, `xml`, `curl`.
No está disponible `sqlite3`, por lo que las pruebas automáticas con base de datos SQLite no se pueden ejecutar en este equipo.

---

## 4. Estado de Git y línea base

### 4.1 Estado inicial

- Repositorio Git presente en `intrasev-motors-main`.
- Rama `master` **sin ningún commit** (`fatal: your current branch 'master' does not have any commits yet`).
- **766 archivos preparados** para el primer commit.
- No era posible usar `git stash` porque todavía no existía `HEAD`.

### 4.2 Acciones realizadas

1. Se configuró una identidad local del repositorio, ya que no había ninguna configurada:
   ```bash
   git config user.name "IntraServ Motors"
   git config user.email "dev@intrasev.local"
   ```
2. Se prepararon todos los archivos y se creó el commit inicial:
   ```bash
   git add -A
   git commit -m "chore: linea base estable del proyecto con configuracion de PHPUnit"
   ```

### 4.3 Resultado

| Dato | Valor |
|---|---|
| Commit | `8508899` |
| Rama | `master` (`HEAD -> master`) |
| Fecha | 2026-11-11 19:48:22 -0500 |
| Autor | IntraServ Motors `<dev@intrasev.local>` |
| Archivos del commit | 769 archivos, 132 395 inserciones |
| Estado final | `nothing to commit, working tree clean` |

> **Nota:** la identidad `IntraServ Motors <dev@intrasev.local>` es local al repositorio y se puede cambiar en cualquier momento con `git config user.name` y `git config user.email`.

> **Nota:** Git avisó de la conversión de fin de línea LF → CRLF en los archivos nuevos (`phpunit.xml.dist` y pruebas). Es un aviso normal en Windows y no afecta al funcionamiento.

---

## 5. Dependencias y PHPUnit

### 5.1 Situación inicial

- `vendor/` contenía solo `bin`, `composer`, `laminas`, `psr` y `autoload.php`.
- No existía `vendor/bin/phpunit`.
- No existían `phpunit.xml`, `phpunit.xml.dist` ni la carpeta `tests/`.
- Al ejecutar `vendor/bin/phpunit` sin configuración, PHPUnit solo mostraba la ayuda de uso y no ejecutaba pruebas.

### 5.2 Acciones realizadas

```bash
composer install --no-interaction --prefer-dist
```

- Se instalaron las dependencias de desarrollo declaradas en `composer.lock`, incluido **PHPUnit 11.5.56**.
- `composer.lock` no se modificó.
- Composer mostró avisos PSR-4 de clases de `system/ThirdParty` (Kint y PSR-Log). Son avisos del framework incluido en `system/` y no impidieron la instalación ni la ejecución de las pruebas.

### 5.3 Configuración de la suite creada

| Archivo | Propósito |
|---|---|
| `phpunit.xml.dist` | Configuración de PHPUnit: bootstrap `system/Test/bootstrap.php`, suites `Unit` y `Feature`, entorno `testing` y `app.baseURL` en `http://localhost:8080/` |
| `tests/unit/EntornoTest.php` | Comprueba que `ENVIRONMENT` es `testing`, que la clase `App\Controllers\Home` se carga y que el servicio `router` existe |
| `tests/feature/SaludTest.php` | Prueba de humo HTTP de `GET /salud`: espera estado 200 y `{"estado":"ok"}`; no usa base de datos |

La configuración usa `system/Test/bootstrap.php` porque este proyecto lleva el framework CodeIgniter dentro de `system/` (no en `vendor/codeigniter4/framework`).

### 5.4 Resultado de la ejecución

```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: ...\phpunit.xml.dist

...                                                                 3 / 3 (100%)

Time: 00:00.044, Memory: 14.00 MB

OK (3 tests, 6 assertions)
```

---

## 6. Arranque del proyecto

### 6.1 Situación inicial

- MySQL (XAMPP) estaba detenido.
- `.env` apuntaba a `app.baseURL = 'http://localhost:8081/'` y a la base de datos `ds_g1_staging`.
- La base `ds_g1_staging` **no existe** en la instancia local de MySQL.
- Sí existe y tiene datos la base de desarrollo `ds_g1` (tablas `tproductos`, `tautos`, `tusuarios`, `tclientes`, `tsolicitudesservicio`, entre otras).

### 6.2 Acciones realizadas

1. Se inició MySQL de XAMPP:
   ```powershell
   Start-Process -FilePath "E:\XAAMP\mysql\bin\mysqld.exe" `
     -ArgumentList "--defaults-file=E:\XAAMP\mysql\bin\my.ini","--standalone" `
     -WorkingDirectory "E:\XAAMP" -WindowStyle Hidden
   ```
   MySQL quedó ejecutándose (proceso `mysqld`, PID 4104 al momento de la comprobación).
2. Se ajustó únicamente el `.env` local (archivo ignorado por Git) para el entorno de desarrollo:
   ```env
   app.baseURL = 'http://localhost:8080/'
   database.default.database = ds_g1
   ```
3. Se levantó el servidor:
   ```bash
   php spark serve --port 8080
   ```

### 6.3 Evidencias de la comprobación

| Petición | Resultado |
|---|---|
| `GET http://localhost:8080/` | **200**, 53 034 bytes, título `<title>INTRASEV MOTORS</title>` |
| `GET http://localhost:8080/salud` | **200**, cuerpo `{"estado": "ok"}` |

El servidor de desarrollo se detuvo después de la comprobación.

---

## 7. Cambios realizados

| Archivo / elemento | Cambio | ¿Versionado en Git? |
|---|---|---|
| `phpunit.xml.dist` | Nuevo: configuración de PHPUnit | Sí |
| `tests/unit/EntornoTest.php` | Nuevo: prueba de humo de arranque | Sí |
| `tests/feature/SaludTest.php` | Nuevo: prueba de humo de `/salud` | Sí |
| `.env` | `baseURL` a 8080 y BD a `ds_g1` | No (ignorado por `.gitignore`) |
| `vendor/` | Dependencias instaladas con Composer | No (ignorado por `.gitignore`) |
| Commit `8508899` | Línea base con los archivos anteriores | Sí |

---

## 8. Reproducción desde cero

Desde la raíz del repositorio (`D:\C BODRIO\intrasev\intrasev-motors-main`), en PowerShell:

```powershell
# 1. Herramientas
php --version
composer --version
git --version

# 2. Estado de Git
git status

# 3. Dependencias y PHPUnit
composer install
vendor\bin\phpunit

# 4. Servidor (requiere MySQL de XAMPP encendido y la BD ds_g1)
php spark serve --port 8080
```

En otra terminal:

```powershell
Invoke-WebRequest -Uri "http://localhost:8080/" -UseBasicParsing | Select-Object StatusCode
Invoke-WebRequest -Uri "http://localhost:8080/salud" -UseBasicParsing | Select-Object StatusCode, Content
```

Detener el servidor con `Ctrl + C`.

---

## 9. Alcance de lo verificado y lo no verificado

### 9.1 Verificado

- Versiones de PHP, Composer, Git y PHPUnit.
- Instalación de dependencias de desarrollo desde `composer.lock`.
- Ejecución de PHPUnit con 3 pruebas y 6 aserciones en verde.
- Arranque de la aplicación con CodeIgniter y conexión a la BD `ds_g1`.
- Renderizado de la página de inicio por HTTP (200 y título correcto).
- Respuesta del endpoint de salud `/salud`.
- Commit inicial creado y árbol de trabajo limpio.

### 9.2 No verificado en este paso

- Navegación manual completa en navegador (login, registro, carrito, panel de administración, módulo de servicios).
- Flujos funcionales de negocio (crear solicitud, validar disponibilidad, consultar por código/placa, checkout).
- Entorno de staging: la BD `ds_g1_staging` no existe, el puerto 8081 no se usó y `CI_ENVIRONMENT` sigue en `development`.
- Migraciones y seeders: no se ejecutaron porque la BD `ds_g1` ya contenía tablas y datos.
- Pruebas automáticas con base de datos (no hay suite de pruebas de BD y la extensión `sqlite3` no está disponible).
- `scripts/smoke_test.ps1` y `scripts/deploy_staging.ps1`: no se ejecutaron en este paso.
- Comportamiento visual, JavaScript, CSS y responsive.

Por tanto, los resultados **OK** de este documento se limitan a las comprobaciones de la sección 9.1.

---

## 10. Notas para los siguientes pasos

1. **Crear una rama de trabajo** a partir de la línea base:
   ```bash
   git switch -c feature/nombre-del-cambio
   ```
2. **Staging (Guía 9):** crear la base `ds_g1_staging`, restaurar en `.env` los valores de staging (`baseURL` en 8081 y BD `ds_g1_staging`) o copiar `env.staging.example`, y ejecutar `scripts/deploy_staging.ps1`.
3. **Identidad de Git:** cambiar `user.name` y `user.email` si se desea usar la identidad personal.
4. **MySQL:** queda en ejecución tras este paso; detenerlo con `E:\XAAMP\mysql_stop.bat` si no se va a usar.
