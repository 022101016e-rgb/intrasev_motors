# Despliegue reproducible en STAGING local (Guía 9, Pasos 1-7) - Windows / PowerShell.
# Uso:  powershell -ExecutionPolicy Bypass -File scripts\deploy_staging.ps1 -Repo <URL> [-Dest proyecto-staging] [-Port 8081]
# Requisitos: git, php >= 8.2, composer y una BD VACÍA (p.ej. ds_g1_staging) creada antes.
# El script se detiene tras crear .env: edítelo (ver env.staging.example) y vuelva a ejecutarlo.
param(
    [Parameter(Mandatory = $true)][string]$Repo,
    [string]$Dest = "proyecto-staging",
    [int]$Port = 8081,
    [switch]$Seed
)
$ErrorActionPreference = "Stop"

if (-not (Test-Path $Dest)) {
    Write-Host "== Paso 2: copia independiente ==" 
    git clone $Repo $Dest
}
Set-Location $Dest
Write-Host ("Commit desplegado: " + (git rev-parse --short HEAD) + " - " + (git log -1 --pretty=%s))

Write-Host "== Paso 3: dependencias desde composer.lock =="
composer install --no-dev --prefer-dist --optimize-autoloader

if (-not (Test-Path ".env")) {
    Copy-Item "env" ".env"
    Write-Host "Se creó .env desde la plantilla. EDÍTELO (CI_ENVIRONMENT=production, baseURL con el puerto $Port, BD de staging) y vuelva a ejecutar este script."
    exit 0
}

Write-Host "== Paso 6: migraciones =="
php spark migrate:status
php spark migrate
php spark migrate:status

if ($Seed) { Write-Host "== Datos de prueba =="; php spark db:seed DatosInicialesSeeder }

Write-Host "== Paso 7: servidor de laboratorio en el puerto $Port (Ctrl+C para detener) =="
Write-Host "En otra consola:  powershell -File scripts\smoke_test.ps1 -Base http://localhost:$Port"
php spark serve --port $Port
