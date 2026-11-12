#!/usr/bin/env bash
# Despliegue reproducible de INTRASEV en una copia de STAGING (Guía 9, Pasos 1-7).
# Uso:   ./scripts/deploy_staging.sh <URL_REPOSITORIO> [carpeta_destino] [puerto]
# Ejemplo: ./scripts/deploy_staging.sh https://github.com/equipo/intrasev-motors.git proyecto-staging 8081
#
# Requisitos: git, php >= 8.2 (ext-intl, mbstring, mysqli), composer, y una BD vacía creada
# previamente (p.ej. ds_g1_staging). El script NO crea la BD ni escribe credenciales: crea
# .env desde la plantilla y se detiene para que usted lo edite (el .env nunca va a Git).
set -euo pipefail

REPO="${1:?Indique la URL del repositorio}"
DEST="${2:-proyecto-staging}"
PORT="${3:-8081}"

echo "== Paso 1: commit que se desplegará (si se ejecuta dentro del repo de desarrollo) =="
git rev-parse --short HEAD 2>/dev/null || echo "(no se ejecuta dentro de un repositorio)"

echo "== Paso 2: copia independiente =="
git clone "$REPO" "$DEST"
cd "$DEST"
echo "Commit desplegado: $(git rev-parse --short HEAD) - $(git log -1 --pretty=%s)"

echo "== Paso 3: dependencias desde composer.lock (sin --dev, sin update) =="
composer install --no-dev --prefer-dist --optimize-autoloader

echo "== Paso 5: archivo .env en destino =="
if [ ! -f .env ]; then
  cp env .env
  echo "Se creó .env a partir de 'env'. EDÍTELO ahora (ver env.staging.example):"
  echo "  CI_ENVIRONMENT=production, app.baseURL=http://localhost:${PORT}/, BD de staging."
  echo "Luego ejecute:  cd $DEST && ./scripts/deploy_staging_continuar.sh $PORT --seed"
  exit 0
fi
