#!/usr/bin/env bash
set -euo pipefail

# Restaura um backup gerado por backup.sh.
# Uso: restore.sh <arquivo.sql.gz>

PROJECT_DIR="${PROJECT_DIR:-/opt/aasscnovo}"
COMPOSE="docker compose -f ${PROJECT_DIR}/docker-compose.prod.yml"

if [ $# -ne 1 ]; then
  echo "Uso: $0 <arquivo.sql.gz>" >&2
  exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "${BACKUP_FILE}" ]; then
  echo "Arquivo não encontrado: ${BACKUP_FILE}" >&2
  exit 1
fi

echo "ATENÇÃO: o banco atual será substituído pelo conteúdo de ${BACKUP_FILE}."
echo "Pressione Ctrl+C em até 5s para cancelar."
sleep 5

gunzip -c "${BACKUP_FILE}" \
  | ${COMPOSE} exec -T db sh -c \
    'MYSQL_PWD="$MARIADB_PASSWORD" mariadb -u"$MARIADB_USER" "$MARIADB_DATABASE"'

echo "Restauração concluída."
