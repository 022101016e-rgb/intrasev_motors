# Pruebas de humo automáticas (Guía 9, F8) para Windows / PowerShell.
# Uso:  powershell -ExecutionPolicy Bypass -File scripts\smoke_test.ps1 [-Base http://localhost:8081]
param([string]$Base = "http://localhost:8081")

$fallos = 0

function Probar([string]$nombre, [string]$ruta, [int]$esperado, [string]$texto = "") {
    $codigo = 0; $cuerpo = ""
    try {
        $r = Invoke-WebRequest -Uri ($Base + $ruta) -UseBasicParsing -MaximumRedirection 0 -ErrorAction Stop
        $codigo = [int]$r.StatusCode; $cuerpo = $r.Content
    } catch {
        if ($_.Exception.Response) { $codigo = [int]$_.Exception.Response.StatusCode }
    }
    $ok = ($codigo -eq $esperado) -and ($texto -eq "" -or $cuerpo.Contains($texto))
    if ($ok) { Write-Host "PASS  $nombre ($ruta -> $codigo)" -ForegroundColor Green }
    else     { Write-Host "FAIL  $nombre ($ruta -> $codigo, esperado $esperado)" -ForegroundColor Red; $script:fallos++ }
}

Probar "SM-01 salud"                 "/salud"                  200 '"estado":"ok"'
Probar "SM-02 portada (consulta BD)" "/"                       200 "INTRASEV"
Probar "SM-02b login accesible"      "/login"                  200
Probar "SM-02c servicios sin sesion" "/servicios"              302
Probar "DEF-02 vehiculos sin sesion" "/servicio/vehiculos/1"   401

""
if ($fallos -eq 0) { "Humo automatico: OK" } else { "Humo automatico: $fallos fallo(s)" }
exit $fallos
