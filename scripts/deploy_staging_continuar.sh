#!/usr/bin/env bash
# Segunda parte del despliegue: migraciones, semillas, humo y servidor local.
# Uso (dentro de la carpeta proyecto-staging, con .env ya editado):
#   ./scripts/deploy_staging_continuar.sh [puerto] [--seed]
set -euo pipefail
PORT="${1:-8081}"
SEED="${2:-}"

grep -q "ds_g1_staging\|staging" .env || { echo "ADVERTENCIA: .env no parece apuntar a una BD de staging. Revise antes de migrar."; read -r -p "¿Continuar? (s/N) " r; [ "$r" = "s" ] || exit 1; }

echo "== Paso 6: migraciones =="
php spark migrate:status
php spark migrate
php spark migrate:status

if [ "$SEED" = "--seed" ]; then
  echo "== Datos de prueba (solo staging/laboratorio) =="
  php spark db:seed DatosInicialesSeeder
fi

echo "== Paso 7: servidor local de laboratorio en el puerto $PORT (Ctrl+C para detener) =="
echo "En otra terminal:  ./scripts/smoke_test.sh http://localhost:$PORT"
php spark serve --port "$PORT"
