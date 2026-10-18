#!/usr/bin/env bash
# Gera o ZIP de instalação para hospedagem (sem .git, .env, sessões, sqlite, logs, mídias de demo).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
STAMP="$(date +%Y%m%d)"
OUT_DIR="${1:-/opt/cursor/artifacts}"
NAME="cm1-hospedagem-${STAMP}"
STAGE="${OUT_DIR}/${NAME}"
ZIP="${OUT_DIR}/${NAME}.zip"

rm -rf "${STAGE}"
mkdir -p "${STAGE}" "${OUT_DIR}"

# Empacota via tar (ignora permissões problemáticas do FS do ambiente)
(
  cd "${ROOT}"
  tar cf - \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='tests' \
    --exclude='sessions/sess_*' \
    --exclude='storage/database.sqlite' \
    --exclude='storage/*.sqlite-journal' \
    --exclude='storage/logs/*' \
    --exclude='storage/downloads/*' \
    --exclude='Public/uploads/articles/*' \
    --exclude='Public/uploads/banners/*' \
    --exclude='Public/uploads/videos/*' \
    --exclude='Public/error_log' \
    --exclude='php-error.log' \
    Config Controllers Core Models Views Public database \
    storage/.htaccess sessions/.htaccess \
    .env.example .htaccess .gitignore .cpanel.yml \
    index.php README.md INSTALACAO.md \
    scripts/build-hosting-package.sh
) | (cd "${STAGE}" && tar xf -)

mkdir -p \
  "${STAGE}/storage/logs" \
  "${STAGE}/storage/downloads" \
  "${STAGE}/sessions" \
  "${STAGE}/Public/uploads/articles" \
  "${STAGE}/Public/uploads/banners" \
  "${STAGE}/Public/uploads/videos"

touch \
  "${STAGE}/storage/logs/.gitkeep" \
  "${STAGE}/storage/downloads/.gitkeep" \
  "${STAGE}/Public/uploads/articles/.gitkeep" \
  "${STAGE}/Public/uploads/banners/.gitkeep" \
  "${STAGE}/Public/uploads/videos/.gitkeep"

# Limpeza residual
find "${STAGE}/Public/uploads" -type f ! -name '.gitkeep' ! -name '.htaccess' -delete 2>/dev/null || true
find "${STAGE}/storage" -type f \( -name '*.sqlite*' -o -name '*.log' \) -delete 2>/dev/null || true
find "${STAGE}/storage/downloads" -type f ! -name '.gitkeep' -delete 2>/dev/null || true
find "${STAGE}/storage/logs" -type f ! -name '.gitkeep' -delete 2>/dev/null || true
find "${STAGE}/sessions" -type f -name 'sess_*' -delete 2>/dev/null || true
rm -f "${STAGE}/Public/error_log" "${STAGE}/.env" 2>/dev/null || true

test -f "${STAGE}/database/install.sql"
test -f "${STAGE}/database/schema.sql"
test -f "${STAGE}/.env.example"
test -f "${STAGE}/INSTALACAO.md"
test -f "${STAGE}/Public/index.php"
test -f "${STAGE}/.htaccess"
test -d "${STAGE}/Controllers"

rm -f "${ZIP}"
(
  cd "${OUT_DIR}"
  zip -rq "${NAME}.zip" "${NAME}"
)
cp -f "${ZIP}" "${OUT_DIR}/cm1-hospedagem-completo.zip"

echo "Pacote gerado:"
ls -lh "${ZIP}" "${OUT_DIR}/cm1-hospedagem-completo.zip"
echo "Total de arquivos: $(find "${STAGE}" -type f | wc -l)"
unzip -l "${ZIP}" | grep -E 'install.sql|INSTALACAO|\.env.example|Public/index.php' || true
if unzip -l "${ZIP}" | grep -E '(^|.*/)\.env$|database\.sqlite|sess_|error_log'; then
  echo "FALHA: vazamento sensível no ZIP"
  exit 1
fi
echo "OK: sem vazamentos sensíveis"
