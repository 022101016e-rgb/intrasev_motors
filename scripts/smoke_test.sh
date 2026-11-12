#!/usr/bin/env bash
# Pruebas de humo automáticas (Guía 9, F8: SM-01, SM-02 y parte de SM-05).
# Uso: ./scripts/smoke_test.sh [URL_BASE]     (por defecto http://localhost:8081)
# Los casos SM-03 (operación válida persiste) y SM-04 (inválida rechaza) se ejecutan
# desde el navegador y se registran en F8 con captura.
BASE="${1:-http://localhost:8081}"
fallos=0

check () {  # nombre url codigo_esperado [texto_esperado]
  local nombre="$1" url="$2" esperado="$3" texto="${4:-}"
  local cuerpo codigo
  cuerpo="$(curl -s -o /tmp/smoke_body.$$ -w '%{http_code}' "$url")"; codigo="$cuerpo"
  if [ "$codigo" = "$esperado" ] && { [ -z "$texto" ] || grep -q "$texto" /tmp/smoke_body.$$; }; then
    echo "PASS  $nombre  ($url -> $codigo)"
  else
    echo "FAIL  $nombre  ($url -> $codigo, esperado $esperado${texto:+ con '$texto'})"; fallos=$((fallos+1))
  fi
}

check "SM-01 salud"               "$BASE/salud"    200 '"estado":"ok"'
check "SM-02 portada (consulta BD)" "$BASE/"        200 'INTRASEV'
check "SM-02b login accesible"     "$BASE/login"    200
# Sin sesión, el módulo debe redirigir al login (no exponer datos ni dar 500)
check "SM-02c servicios sin sesión" "$BASE/servicios" 302
# DEF-02: el endpoint de vehículos ya no debe responder datos sin sesión
check "DEF-02 vehiculos sin sesión" "$BASE/servicio/vehiculos/1" 401

rm -f /tmp/smoke_body.$$
echo; [ "$fallos" -eq 0 ] && echo "Humo automático: OK" || echo "Humo automático: $fallos fallo(s)"
exit "$fallos"
