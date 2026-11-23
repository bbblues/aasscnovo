#!/usr/bin/env bash
set -euo pipefail

# Emissão inicial do certificado Let's Encrypt (desafio HTTP-01 via webroot).
# Executar na VPS, na raiz do projeto, com o DNS de sis.aassc.com.br (e www)
# já apontando para o IP da VPS e as portas 80/443 liberadas (ufw).
#
# Uso:
#   export LETSENCRYPT_EMAIL="admin@aassc.com.br"
#   bash docker/nginx/init-letsencrypt.sh

PROJECT_DIR="${PROJECT_DIR:-$(pwd)}"
COMPOSE="docker compose -f ${PROJECT_DIR}/docker-compose.prod.yml"
CERT_DIR="${PROJECT_DIR}/certbot/conf/live/sis.aassc.com.br"
EMAIL="${LETSENCRYPT_EMAIL:-}"

if [ -z "${EMAIL}" ]; then
  echo "ERRO: defina LETSENCRYPT_EMAIL (ex.: export LETSENCRYPT_EMAIL=admin@aassc.com.br)." >&2
  exit 1
fi

mkdir -p "${PROJECT_DIR}/certbot/www" "${PROJECT_DIR}/certbot/conf"

# Se o certificado já existe, apenas garante que a stack está no ar.
if [ -f "${CERT_DIR}/fullchain.pem" ]; then
  echo "Certificado já existe em ${CERT_DIR}; subindo a stack."
  ${COMPOSE} up -d
  exit 0
fi

# 1. Certificado autoassinado temporário para o Nginx conseguir iniciar.
echo "Gerando certificado temporário (autoassinado)..."
mkdir -p "${CERT_DIR}"
openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
  -keyout "${CERT_DIR}/privkey.pem" \
  -out "${CERT_DIR}/fullchain.pem" \
  -subj "/CN=sis.aassc.com.br" >/dev/null 2>&1

# 2. Sobe a stack com o certificado temporário.
echo "Subindo a stack..."
${COMPOSE} up -d

# 3. Remove o temporário e solicita o certificado real (desafio HTTP-01).
echo "Solicitando o certificado real via certbot..."
rm -rf "${CERT_DIR}"
docker run --rm \
  -v "${PROJECT_DIR}/certbot/www:/var/www/certbot" \
  -v "${PROJECT_DIR}/certbot/conf:/etc/letsencrypt" \
  certbot/certbot certonly --webroot -w /var/www/certbot \
  --email "${EMAIL}" --agree-tos --no-eff-email \
  -d sis.aassc.com.br -d www.sis.aassc.com.br

# 4. Recarrega o Nginx para usar o certificado recém-emitido.
echo "Recarregando o Nginx..."
${COMPOSE} exec -T nginx nginx -s reload

echo "OK: certificado emitido para sis.aassc.com.br e www.sis.aassc.com.br."
