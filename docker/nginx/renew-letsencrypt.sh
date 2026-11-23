#!/usr/bin/env bash
set -euo pipefail

# Renova o certificado Let's Encrypt (cron) e recarrega o Nginx.
PROJECT_DIR="${PROJECT_DIR:-/opt/aasscnovo}"
COMPOSE="docker compose -f ${PROJECT_DIR}/docker-compose.prod.yml"

docker run --rm \
  -v "${PROJECT_DIR}/certbot/www:/var/www/certbot" \
  -v "${PROJECT_DIR}/certbot/conf:/etc/letsencrypt" \
  certbot/certbot renew --quiet

${COMPOSE} exec -T nginx nginx -s reload
