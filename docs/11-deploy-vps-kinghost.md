# 11 — Deploy em VPS dedicada (KingHost)

> Premissas: VPS dedicada KingHost com acesso root (SSH), sistema Linux
> (Ubuntu/Debian), apto a rodar Docker. Confirmar com a KingHost o SO e se há
> painel/firewall próprio.

## Arquitetura alvo

```
Internet
   │ HTTPS (443)
   ▼
Nginx (reverse proxy + TLS) ──► container web (PHP-FPM/Apache + CI4)
                                   │
                                   └──► container db (MariaDB) [volume persistente]
```

## Etapas de provisionamento

1. **Sistema e segurança básica**
   - Atualizar pacotes, criar usuário não-root com sudo, configurar SSH (chave),
     desativar login por senha no root.
   - Firewall: liberar apenas 22, 80 e 443 (ex.: `ufw allow OpenSSH` etc.).
   - `fail2ban` (opcional) para SSH.
2. **Instalar Docker + Docker Compose** (plugin `docker compose`).
3. **Subir a stack** (semelhante ao `10-ambiente-docker.md`, mas em modo produção):
   - `CI_ENVIRONMENT = production`
   - Banco com credenciais fortes e volume persistente.
   - Portas internas (não expor 3306 publicamente).
4. **Nginx como reverse proxy**
   - Redirecionar 80 → 443.
   - Certificado TLS via **Let's Encrypt** (certbot) para `sis.aassc.com.br`.
   - `client_max_body_size` adequado (uploads, se houver).
5. **DNS**
   - Apontar `sis.aassc.com.br` (e `www`) para o IP da VPS.

## Banco de dados em produção

- **Opção A (recomendada p/ controle):** rodar MariaDB em container com volume
  persistente e importar o dump + dados atuais.
- **Opção B:** usar o MySQL/MariaDB já existente da KingHost e apontar o `.env`
  da aplicação para ele (mantém os dados atuais sem mover).
  - Independentemente da opção, **backups diários** são obrigatórios.

## Backups

- `mysqldump` agendado (cron) diário, armazenado fora do disco principal
  (ex.: volume secundário, storage externo ou outro servidor).
- Reter N dias; testar restauração periodicamente.
- Backup também dos volumes/compose e do `.env` (com cuidado com segredos).

## CI/CD (opcional, recomendado)

- Build da imagem e deploy via Git (ex.: GitHub Actions/GitLab CI) com
  `docker compose up -d --build` na VPS, ou deploy manual via `rsync` + restart.
- Guardar segredos em variáveis de ambiente/secretos, nunca no repositório.

## Checklist de produção (CI4)

- `CI_ENVIRONMENT = production`; logs em `writable/logs`.
- `app.baseURL = https://sis.aassc.com.br`.
- CSRF habilitado; sessão com cookies `Secure`/`HttpOnly`.
- Credenciais via `.env` (arquivo protegido, fora do webroot).
- Desativar exibição de erros; manter logging.
- Considerar `opcache` habilitado no PHP do container.

## Domínios (referência)
- Produção: `sis.aassc.com.br`
- (teste atual): `teste.aassc.com.br`
