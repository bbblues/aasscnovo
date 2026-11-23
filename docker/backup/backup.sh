#!/usr/bin/env bash
set -euo pipefail

# Backup diário do MariaDB (cron). Gera dump compactado e aplica retenção.
# As credenciais são lidas do ambiente do container "db" (não ficam no script).

PROJECT_DIR="${PROJECT_DIR:-/opt/aasscnovo}"
BACKUP_DIR="${BACKUP_DIR:-${PROJECT_DIR}/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
COMPOSE="docker compose -f ${PROJECT_DIR}/docker-compose.prod.yml"

mkdir -p "${BACKUP_DIR}"

STAMP="$(date +%Y%m%d_%H%M%S)"
FILE="${BACKUP_DIR}/aassc_${STAMP}.sql.gz"

echo "[$(date '+%F %T')] Backup em ${FILE}"

# Executa o dump DENTRO do container db, usando as credenciais já presentes
# no ambiente do container (MARIADB_USER/MARIADB_PASSWORD/MARIADB_DATABASE).
${COMPOSE} exec -T db sh -c \
  'MYSQL_PWD="$MARIADB_PASSWORD" mariadb-dump --single-transaction --quick --routines --triggers -u"$MARIADB_USER" "$MARIADB_DATABASE"' \
  | gzip > "${FILE}"

echo "[$(date '+%F %T')] Backup concluído (${FILE})."

# Retenção: remove backups com mais de N dias.
find "${BACKUP_DIR}" -name 'aassc_*.sql.gz' -mtime +"${RETENTION_DAYS}" -delete

echo "[$(date '+%F %T')] Retenção aplicada (${RETENTION_DAYS} dias)."
