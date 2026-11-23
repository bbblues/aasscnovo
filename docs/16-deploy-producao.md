# 16 — Deploy em produção (VPS KingHost)

> Runbook da **Sprint 8** (ver `13-plano-de-sprints.md` e `11-deploy-vps-kinghost.md`).
> Esta sprint entrega os **artefatos** de deploy; a execução real exige acesso
> à VPS da KingHost (SSH), domínio `sis.aassc.com.br` e credenciais de produção.

## Decisão D6 — banco de dados em produção

**Opção escolhida: A — MariaDB em container com volume persistente** (recomendada
em `11-deploy-vps-kinghost.md`). A aplicação continua apontando para o serviço
`db` da rede Docker; o dump legado é importado uma única vez no volume
`db_data`.

- Vantagens: controle total, sem dependência do MySQL já existente da KingHost,
  consistência entre dev/prod, backup/restore simples via container.
- Alternativa (B): apontar `database.default.*` no `.env` para o MySQL existente
  da KingHost e **remover o serviço `db`** do `docker-compose.prod.yml`. Nesse
  caso o backup passa a ser do banco da KingHost (fora do container).

## Artefatos criados nesta sprint

| Arquivo | Papel |
| --- | --- |
| `docker-compose.prod.yml` | Stack prod (web + db + nginx), sem expor 3306 |
| `docker/php/prod.Dockerfile` | Imagem prod com opcache + hardening Apache |
| `docker/php/prod-php.ini` | `display_errors=Off`, `log_errors=On`, opcache |
| `docker/php/prod-security.conf` | `ServerTokens Prod`, sem `TRACE` |
| `docker/php/prod-000-default.conf` | vhost sem listagem de diretório |
| `docker/nginx/nginx.conf` + `conf.d/sis.aassc.com.br.conf` | Reverse proxy, 80→443, TLS |
| `docker/nginx/init-letsencrypt.sh` | Emissão inicial do certificado |
| `docker/nginx/renew-letsencrypt.sh` | Renovação (cron) + reload Nginx |
| `docker/backup/backup.sh` / `restore.sh` | `mariadb-dump` com retenção / restauração |
| `docker/backup/crontab` | Backups diários + renovação de certificado |
| `.env.production.example` | Template do `.env` de produção |
| `.github/workflows/deploy.yml` | CI/CD opcional (testes + rsync + rebuild) |

Mudanças no código: `secureheaders` habilitado em `app/Config/Filters.php` e
`app.proxyIPs` configurado (IP real do cliente atrás do proxy).

## 1. Provisionamento da VPS (uma vez)

```bash
# Acesso root; ajuste para o seu usuário/domínio.
# 1. Atualizar e criar usuário não-root com sudo
apt update && apt upgrade -y
adduser deploy
usermod -aG sudo deploy

# 2. SSH por chave (a partir da sua máquina)
ssh-copy-id deploy@IP_DA_VPS
# em /etc/ssh/sshd_config: PasswordAuthentication no, PermitRootLogin no
sudo systemctl reload ssh

# 3. Firewall: só 22, 80 e 443
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# 4. (Opcional) fail2ban para SSH
sudo apt install -y fail2ban
sudo systemctl enable --now fail2ban

# 5. Docker + Compose plugin
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $VERSION_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list >/dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl enable --now docker
sudo usermod -aG docker deploy

# 6. openssl (usado no init-letsencrypt.sh)
sudo apt install -y openssl
```

## 2. Deploy inicial

```bash
# Na VPS, como usuário deploy.
sudo mkdir -p /opt/aasscnovo && sudo chown deploy:deploy /opt/aasscnovo
cd /opt/aasscnovo

# Clone (ou rsync/git pull do seu fluxo de deploy)
git clone https://github.com/bbblues/aasscnovo.git .

# Ambiente de produção
cp .env.production.example .env
# EDITAR .env: trocar senhas (MARIADB_PASSWORD, MARIADB_ROOT_PASSWORD,
# database.default.password) por valores fortes.

# Build e subida (web + db + nginx)
docker compose -f docker-compose.prod.yml up -d --build

# Instalar dependências PHP (vendor/ não é versionado)
docker compose -f docker-compose.prod.yml run --rm web composer install --no-dev --optimize-autoloader --no-interaction

# Importar o dump legado (schema + dados) no volume persistente
docker compose -f docker-compose.prod.yml exec -T db sh -c \
  'MYSQL_PWD="$MARIADB_PASSWORD" mariadb -u"$MARIADB_USER" "$MARIADB_DATABASE"' \
  < docs/_referencia/aassc.sql
```

> **Importante:** como no ambiente local, **não** rode `php spark migrate` sobre
> o banco importado — as migrações de criação recriariam tabelas já existentes.

## 3. DNS

No painel de DNS (KingHost), crie/aponte:

- `A` `sis.aassc.com.br` → IP da VPS
- `A` (ou `CNAME`) `www.sis.aassc.com.br` → IP da VPS

Aguarde a propagação (`dig +short sis.aassc.com.br` deve retornar o IP).

## 4. HTTPS (Let's Encrypt)

```bash
cd /opt/aasscnovo
export LETSENCRYPT_EMAIL="admin@aassc.com.br"
bash docker/nginx/init-letsencrypt.sh
```

O script gera um certificado temporário para o Nginx iniciar, sobe a stack,
emite o certificado real via webroot e recarrega o Nginx.

## 5. Backups

```bash
# Instala o crontab (ajuste os caminhos se o projeto não estiver em /opt/aasscnovo)
crontab docker/backup/crontab

# Backup manual (teste imediato)
PROJECT_DIR=/opt/aasscnovo bash docker/backup/backup.sh

# Restauração (teste)
PROJECT_DIR=/opt/aasscnovo bash docker/backup/restore.sh /opt/aasscnovo/backups/aassc_XXXX.sql.gz
```

> Retenção padrão: 14 dias (`RETENTION_DAYS`). Armazene cópias fora do disco
> principal (ex.: outro volume/storage externo).

## 6. Cutover

Quando `https://sis.aassc.com.br` estiver respondendo com HTTPS e o login
funcionar com os dados importados, o cutover é apenas apontar o DNS do domínio
de produção para a VPS (etapa 3) — de preferência em janela de baixo tráfego.

## 7. CI/CD (opcional)

Configure os secrets do repositório (`DEPLOY_HOST`, `DEPLOY_USER`,
`DEPLOY_SSH_KEY`, `DEPLOY_PATH`) e o workflow `.github/workflows/deploy.yml`
passa a rodar testes e publicar via rsync + rebuild a cada push em `main`.

## Checklist de produção (CI4)

- [x] `CI_ENVIRONMENT = production` (`env_file: .env` + `environment:` no compose)
- [x] `app.baseURL = https://sis.aassc.com.br/` (`.env.production.example`)
- [x] Logs em `writable/logs` (volume nomeado `app_writable`)
- [x] opcache habilitado (`prod-php.ini`)
- [x] Erros ocultos em produção (`display_errors=Off`; CI4 esconde detalhes)
- [x] Cookies `Secure`/`HttpOnly` (já configurado em `Config\Cookie`)
- [x] `secureheaders` habilitado (`X-Frame-Options`, `X-Content-Type-Options`, etc.)
- [x] IP real do cliente via `app.proxyIPs` + `X-Real-IP`
- [x] Credenciais via `.env` (fora do webroot e do git)

