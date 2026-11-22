# 00 — Visão Geral

## O que é o sistema

Sistema interno de gestão de **solicitações / atendimentos** da AASSC
(Associação — aparentemente ligada a demandas de saúde/assistência social
encaminhadas por parlamentares/clientes).

O sistema permite:

- Cadastrar **pessoas** (beneficiários) e seus telefones.
- Abrir e acompanhar **solicitações** (pedidos) vinculadas a uma pessoa, com
  agente responsável, procedimento, situação e observações.
- Registrar **comentários/atualizações** em cada solicitação.
- Listar e **filtrar** pessoas, solicitações e atualizações.
- Visualizar um **dashboard** com gráficos e um **relatório de atividades**.

## Perfis de usuário (grupos)

Baseados na tabela `users_groups` (Ion Auth):

| Grupo | Nome (atual) | Papel |
| --- | --- | --- |
| 1 | admin | Administrador (gestão de usuários/grupos) |
| 2 | agente | Agente que atende as solicitações |
| 4 | cliente | Cliente (parlamentar/órgão) dono das pessoas |

> Observação: o grupo `3` não é usado no código atual. Os nomes reais dos
> grupos na tabela `groups` devem ser confirmados; o código referencia os IDs
> 1, 2 e 4 diretamente.

## Stack atual (CodeIgniter 3)

- PHP (>= 5.3.7 no `composer.json`; ambiente de produção usa PHP 8.3 no host).
- CodeIgniter 3 (sistema clássico, sem Composer/namespaces).
- Banco MySQL/MariaDB (produção: `mysql.aassc.com.br`, db `aassc`; teste:
  `mysql.teste.aassc.com.br`, db `teste20`).
- Front-end: **Bootstrap 5.3.3** (CDN) + **Chart.js** (CDN) + CSS customizado
  (`application/views/css.php`). Assets antigos de Materialize/jQuery UI ainda
  existem mas não são usados.
- Autenticação: **Ion Auth 2.5.2** (CI3) com bcrypt.
- Sessões em arquivo (`files`), cookie `ci_session`.

## Stack alvo (CodeIgniter 4)

- PHP 8.1+ (recomendado 8.2/8.3).
- CodeIgniter 4 (Composer + namespaces, migrações, filters, `.env`).
- Banco: **manter o schema atual** (ver `09-estrategia-de-banco.md`).
- Autenticação: substituir Ion Auth (ver `04-autenticacao-e-permissoes.md`).
- Front-end: manter Bootstrap 5.x + Chart.js; CSS customizado migrado para um
  arquivo `.css` próprio (não mais inline via PHP).
- Ambientes:
  - Desenvolvimento local: **Docker** (`10-ambiente-docker.md`).
  - Produção: **VPS dedicada KingHost** (`11-deploy-vps-kinghost.md`).

## Domínios e ambiente (referência encontrada)

- Produção: `https://sis.aassc.com.br` (`$config['base_url']`).
- Teste (comentado no config): `https://teste.aassc.com.br`.
- Banco produção: host `mysql.aassc.com.br`, db `aassc`.
- Banco teste: host `mysql.teste.aassc.com.br`, db `teste20`.

> As credenciais de banco estão **hardcoded** no `application/config/database.php`.
> No novo projeto devem ir para `.env` e ficar **fora do repositório**.

## Escopo da reescrita

Reescrever 100% das funcionalidades atualmente em uso (ver
`02-requisitos-funcionais.md`), descartando código morto/legado (ver
`08-legado-e-divida-tecnica.md`), mantendo o banco compatível.
