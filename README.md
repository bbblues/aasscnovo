# AASSC — Sistema de Solicitações e Atendimentos

Reescrita do sistema interno da **AASSC** (CodeIgniter 3 → CodeIgniter 4).

O sistema cadastra **pessoas** atendidas, registra **solicitações** vinculadas a
essas pessoas (agente responsável, procedimento, situação, observações), guarda
**comentários/atualizações** de cada solicitação e oferece listagens filtradas,
**dashboard** com gráficos e **relatório de atividades**.

Especificação completa em [`docs/`](docs/README.md) — fonte única de verdade da reescrita.

## Status

| Sprint | Tema | Situação |
| --- | --- | --- |
| 0 | Kickoff e definições | ⚠️ parcial (decisões D1–D7 em aberto) |
| 1 | Bootstrap do CodeIgniter 4 + Docker | ✅ concluída |
| 2 | Banco de dados e camada de dados | ✅ concluída |
| 3 | Autenticação e autorização | ✅ concluída |
| 4 | Layout base + dashboard + gráficos | ✅ concluída |
| 5 | Pessoas + Solicitações (núcleo do negócio) | ✅ concluída |
| 6 | Comentários + Relatório + CRUDs auxiliares | ✅ concluída |
| 7 | Qualidade, validação e segurança | ✅ concluída |
| 8 | Deploy em produção | ⏳ planejada |

Plano detalhado: [`docs/13-plano-de-sprints.md`](docs/13-plano-de-sprints.md).

## Stack

- PHP 8.2+ com CodeIgniter 4
- MariaDB 10.11 (schema legado preservado)
- Bootstrap 5 + Chart.js no front-end
- PHPUnit 10 para testes
- Docker / Docker Compose no ambiente local
- Produção prevista em VPS dedicada KingHost ([`docs/11-deploy-vps-kinghost.md`](docs/11-deploy-vps-kinghost.md))

## Como rodar localmente (Docker)

Pré-requisitos: Docker + Docker Compose.

```bash
# 1. Clone o repositório
git clone https://github.com/bbblues/aasscnovo.git
cd aasscnovo

# 2. Crie o .env a partir do exemplo
cp .env.example .env          # PowerShell: Copy-Item .env.example .env

# 3. Suba os containers
docker compose up -d --build

# 4. Instale as dependências PHP (vendor/ não é versionado)
docker compose run --rm web composer install

# 5. Importe o banco original (schema + dados do legado)
docker compose exec -T db mysql -uaassc -paassc aassc < docs/_referencia/aassc.sql

# 6. Acesse http://localhost:8080
```

Serviços expostos:

| Serviço | URL | Observação |
| --- | --- | --- |
| Aplicação | http://localhost:8080 | Apache com DocumentRoot em `public/` |
| Adminer | http://localhost:8081 | servidor `db`, usuário `aassc`, senha `aassc` |
| MariaDB | localhost:3306 | banco `aassc` |

> O banco original já contém o schema e os dados. **Não** rode
> `php spark migrate` sobre ele — as migrações de criação tentariam recriar
> tabelas existentes e falhariam.

### Criar um banco vazio (opcional, só para validar as migrações)

Para reproduzir o schema **do zero, sem dados**, use um volume novo:

```bash
docker compose down -v              # apaga o volume de dados atual
docker compose up -d --build
docker compose run --rm web composer install
docker compose exec web php spark migrate
```

## Banco de dados

- **Fonte de verdade:** o banco original do legado (`docs/_referencia/aassc.sql`).
  A aplicação apenas lê/escreve nele; o schema **não** é alterado pela aplicação.
- As migrações em `app/Database/Migrations` existem **apenas** para reproduzir um
  schema **novo e vazio** (dev/teste). Em qualquer banco que já tenha o schema ou
  dados, **não** rode as migrações de criação
  (ver [`docs/09-estrategia-de-banco.md`](docs/09-estrategia-de-banco.md)).
- Credenciais ficam no `.env` (fora do versionamento); `.env.example` só
  documenta os valores padrão do Docker.

## Testes

```bash
docker compose exec web composer test
# equivalente a:
docker compose exec web php vendor/bin/phpunit
```

Os testes usam SQLite em memória (sem depender de um banco MySQL externo):

- `tests/unit` — funções auxiliares e configurações de segurança.
- `tests/database` — Models (`Pessoa`, `Solicitacao`, `Comentario`, `Usuario`,
  `UsuarioGrupo`) e o serviço `Autenticacao` (login, rate limiting, reset).
- `tests/feature` — fluxo de autenticação via HTTP (filtros `auth`/`group`).

Os dados de teste ficam em `tests/_support/Database/Seeds/Sprint7TestSeeder.php`
e o schema portável em `tests/_support/Database/Migrations/`.

## Estrutura do projeto

```
app/
  Config/           configuração do CodeIgniter 4
  Controllers/      controllers (por enquanto apenas Home)
  Database/         migrações e seeds
  Helpers/          funcoes_helper.php
  Models/           Models do domínio (Pessoa, Solicitacao, Usuario, ...)
docs/               especificação funcional e técnica
docker/php/         Dockerfile e vhost do Apache
public/             webroot da aplicação
tests/              testes com PHPUnit
writable/           cache, logs e sessões (conteúdo ignorado pelo git)
```

## Segurança

- `.env` e `vendor/` não são versionados.
- Nunca comite credenciais de produção: use variáveis de ambiente.

## Licença

MIT — veja [`LICENSE`](LICENSE).
