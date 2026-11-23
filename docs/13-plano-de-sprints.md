# 13 — Plano de Sprints

> Derivado de `12-roadmap.md`, com granularidade ágil (objetivo, entregáveis,
> critérios de aceite, dependências e estimativa por sprint).
>
> Repositório: https://github.com/bbblues/aasscnovo

## Premissas adotadas

- **Time:** 1 desenvolvedor, sprints de ~1 semana (ajustável conforme D7).
- **Escopo:** reescrever do zero em CodeIgniter 4, usando esta pasta `docs/`
  como fonte única de verdade.
- **Banco:** manter o schema atual, sem migração de dados (ver `09`).
- **Auth:** substituir Ion Auth; recomendação preliminar = **auth customizada
  (opção 3)** sobre `users`/`users_groups`/`groups` (ver `04` e decisão D1).
- **Ambientes:** Docker local (`10`); produção VPS KingHost (`11`).

## Decisões em aberto (bloqueiam sprints)

| # | Decisão | Impacto | Bloqueia |
| --- | --- | --- | --- |
| D1 | Auth: **custom (opção 3)** vs port Ion Auth (opção 2) vs Shield (opção 1) | escopo da Sprint 3 | Sprint 3 |
| D2 | Nomes reais dos grupos, papel do grupo 3 e **acesso do "cliente"** | regras de autorização | Sprint 3 |
| D3 | Período dos gráficos (**ano vs mês**) — hoje chama `sol_por_cliente(365)` para ambos | lógica do dashboard | Sprint 4 |
| D4 | Uso de `uc`, `pes_user`, `pes_catpessoa` em `pessoas` | formulário de pessoa | Sprint 5 |
| D5 | CRUDs auxiliares (cidades/procedimentos/catpessoas): **expor no menu** ou manter só como lookup? | escopo Sprint 6 | Sprint 6 |
| D6 | Produção: **container MariaDB** vs **banco existente KingHost** | provisionamento | Sprint 8 |
| D7 | Duração da sprint / tamanho do time | estimativas | todas |

> Recomendado confirmar D1–D4 antes ou durante a Sprint 1.

## Visão geral das sprints

| Sprint | Tema | Entrega principal |
| --- | --- | --- |
| 0 | Kickoff & definições | decisões fechadas + repositório versionado |
| 1 | Bootstrap + Docker | projeto CI4 sobe com `docker compose up` |
| 2 | Banco + camada de dados | migrações, Models e helpers |
| 3 | Autenticação e autorização | login/logout/reset + filters + gestão de usuários |
| 4 | Layout + Dashboard | casca visual + dashboard com gráficos |
| 5 | Pessoas + Solicitações | núcleo do negócio (cadastro/atendimento) |
| 6 | Comentários + Relatório + CRUDs | paridade funcional completa |
| 7 | Qualidade, validação e segurança | testes + validação lado a lado + revisão |
| 8 | Deploy em produção | VPS KingHost com HTTPS e backups |

---

## Sprint 0 — Kickoff & definições (meio período)

**Objetivo:** destravar o projeto e fechar decisões.

- [ ] Fazer backup definitivo do projeto CI3 (Git/zip) antes de apagar.
- [ ] Confirmar D1–D7.
- [ ] Inicializar repositório (`git init`), branch padrão, `.gitignore`.
- [ ] Registrar decisões no repositório (ex.: `docs/decisoes.md`).

**Critério de aceite:** nenhuma decisão bloqueante pendente para as sprints seguintes.

---

## Sprint 1 — Bootstrap do projeto + Docker

**Objetivo:** subir o CI4 localmente de forma reproduzível.

- [ ] `composer create-project codeigniter4/appstarter .`
- [ ] Docker: `docker/php/Dockerfile` (php:8.2-apache + `mod_rewrite` + extensões
      `intl`, `mbstring`, `pdo_mysql`, `zip`, `gd`) e `docker-compose.yml`
      (web + db MariaDB 10.11 + adminer opcional).
- [ ] `.env` / `.env.example` (banco apontando para o serviço `db`);
      `.gitignore` (`.env` fora do repositório).
- [ ] Importar schema: `docs/_referencia/aassc.sql`.
- [ ] Validar "Hello World" em `http://localhost:8080`.

**Critério de aceite:** `docker compose up -d --build` sobe aplicação + banco sem
passos manuais de PHP/MySQL; banco acessível via adminer.

---

## Sprint 2 — Banco de dados + camada de dados

**Objetivo:** reproduzir o schema e mapear a persistência.

- [ ] Migrações espelhando o schema (`app/Database/Migrations`): `catpessoa`,
      `cidades`, `comment`, `groups`, `login_attempts`, `pessoas`,
      `procedimentos`, `situacoes`, `solicitacoes`, `telefones`, `users`,
      `users_groups` (PKs, índices e AUTO_INCREMENT fiéis ao dump). Não recriar
      `node`/`field_data_comment_body` (legado Drupal).
- [ ] Models (`$table`, `$primaryKey`, `$returnType`, `$allowedFields`,
      `$useTimestamps = false`): `Pessoa`, `Solicitacao`, `Comentario` (tabela
      `comment`), `Telefone`, `Usuario`/`User`, `Cidade`, `Procedimento`,
      `CategoriaPessoa`, `Situacao` (+ `Grupo`/`UsuarioGrupo` se necessário).
- [ ] Helpers (`app/Helpers/funcoes_helper.php`): `config_paginator`,
      `grafico_barras`, `grafico_barras_absoluto`, `situacao_by_id`,
      `data_final`, `remover_valores_vazios`, `separa_post`, `simple_lower`.
- [ ] `SET SQL_BIG_SELECTS=1` na conexão (ou índices adequados).
- [ ] Índices aditivos (se aprovado): `solicitacoes.spessoa/sagente/sol_sit_id/criado`,
      `pessoas.pes_cliente/pes_pub`, `comment.nid/changed`.

**Critério de aceite:** migrações rodam do zero gerando schema idêntico ao dump;
Models retornam dados corretos de `pessoas`, `solicitacoes` e `comment`.

---

## Sprint 3 — Autenticação e autorização

**Objetivo:** substituir Ion Auth com segurança melhorada (ver `04`).

- [ ] Login por **e-mail** + senha (`password_verify`, bcrypt) e logout.
- [ ] Troca de senha (logado) e esqueci/reset com **token + expiração**
      (hoje `use_ci_email=FALSE` e expiração 0 — corrigir).
- [ ] Filters centralizados: `auth` (requer login) em todas as áreas exceto
      login/recuperação; `group:admin` para `/auth`.
- [ ] CSRF habilitado e sessão segura (`HttpOnly`/`Secure`).
- [ ] Rate limiting / bloqueio de tentativas de login.
- [ ] Gestão de usuários/grupos (admin): listar, criar, editar, ativar/desativar,
      criar/editar grupos.
- [ ] Regras de autorização por grupo (D2) sem checagem repetida em construtores.

**Critério de aceite:** login/logout/reset funcionando; rotas bloqueadas para
não-autenticados; `/auth` restrito a admin; senhas bcrypt antigas continuam válidas.

---

## Sprint 4 — Layout base + Dashboard + gráficos

**Objetivo:** montar a casca visual e a home.

- [ ] Layout base (topo/menu/rodapé) migrado para views CI4; CSS customizado
      (`views/css.php`) vira `public/assets/css/` estático.
- [ ] Assets copiados para `public/assets/` (ícone, `procurar.svg`, avatares).
- [ ] Menu navbar: Pessoas (Listar/Cadastrar), Solicitações (Lista Geral/Em Aberto),
      Relatórios, campo "Buscar pessoa".
- [ ] Dashboard (`/`): tabela "Suas solicitações em aberto" (`sagente = logado`,
      `sol_sit_id != 3`) + 3 gráficos Chart.js (2 barras + 1 doughnut), com
      agrupamento em "outros" (>8) e exclusão `users.id IN (76,74)`.
- [ ] Aplicar D3 (período ano/mês).

**Critério de aceite:** dashboard carrega com dados reais e gráficos renderizados;
layout responsivo com Bootstrap 5.

---

## Sprint 5 — Pessoas + Solicitações (núcleo do negócio)

**Objetivo:** implementar o coração do sistema.

- [x] Pessoas: listar (`/pessoas`) com filtros (responsável, cliente, pessoa,
      município, período) persistidos em sessão + "Limpar" + paginação (20/pág);
      editar (`pessoas/edit/{id}`) atualizando pessoa + telefone.
- [x] Solicitações: lista geral (`/solicitacoes`), em aberto (`/solicitacoes/abertas`),
      editar (`/solicitacoes/edit/{pes_id}`) em 2 colunas (cadastro + accordion),
      atualizar (`update/{sid}`), cadastrar (`add`), cadastrar pessoa com
      solicitação opcional (`add_pessoa` usando `separa_post`).
- [x] Regras: `situacao` derivada de `sol_sit_id`; `criado = date('Y-m-d')` e
      `created = UNIX_TIMESTAMP()`; `confirm()` antes de atualizar.
- [x] Campos de pessoa conforme `02` (incl. decisão D4).

**Critério de aceite:** UC-03, UC-04, UC-05, UC-06 funcionam ponta a ponta;
filtros persistem em sessão.

---

## Sprint 6 — Comentários + Relatório + CRUDs auxiliares

**Objetivo:** fechar a paridade funcional.

- [x] Comentários: listar (`/coments`, avatar via `simple_lower(name)`), adicionar
      (`add/{pessoa}`), editar (`update/{cid}`) — edição inline somente pelo autor.
- [x] Relatório de atividades (`/relatorios`): mescla comentários + aberturas de
      solicitação, ordenado por `created` (`usort`), filtros idênticos.
- [x] CRUDs auxiliares: `cidades`, `procedimentos`, `catpessoas`
      (listar/add/edit/remove), conforme D5.

**Critério de aceite:** UC-07 e UC-08 completos; relatório consolidado correto;
CRUDs auxiliares operantes.

---

## Sprint 7 — Qualidade, validação e segurança

**Objetivo:** garantir robustez antes do deploy.

- [ ] Testes automatizados (PHPUnit): Models + fluxos críticos (auth,
      cadastro/edição de solicitação, filtros).
- [ ] Validação lado a lado com o sistema atual (mesmos dados, mesmos resultados).
- [ ] Revisão de segurança: CSRF, binding SQL (`INTERVAL $dias` → binding),
      credenciais em `.env`, sessão segura, rate limiting.
- [ ] Ajustes finos (formatação de datas, ordenação `TRIM(pes_nome)`, etc.).

**Critério de aceite:** testes verdes; resultados batem com o CI3; nenhum risco
de segurança crítico aberto.

---

## Sprint 8 — Deploy em produção (VPS KingHost)

**Objetivo:** colocar em produção com segurança (ver `11`).

- [ ] Provisionar VPS (Docker + Compose), segurança básica (usuário não-root,
      SSH por chave, `ufw`, `fail2ban` opcional).
- [ ] Stack produção: `CI_ENVIRONMENT=production`, credenciais fortes, volume
      persistente (decisão D6).
- [ ] Nginx reverse proxy: 80→443, TLS Let's Encrypt (`sis.aassc.com.br`),
      `client_max_body_size`.
- [ ] DNS apontando para a VPS.
- [ ] Backups diários (`mysqldump` via cron) + teste de restauração.
- [ ] Deploy e cutover (trocar `sis.aassc.com.br` para o novo sistema).
- [ ] CI/CD opcional (Git Actions / rsync + restart).

**Critério de aceite:** checklist de produção do CI4 atendido (baseURL, logs,
opcache, erros ocultos, cookies seguros); HTTPS e backups ativos.

---

## Dependências e ordem recomendada

```
Sprint 0 ─► 1 ─► 2 ─► 3 ─► 4 ─► 5 ─► 6 ─► 7 ─► 8
```

- Sprint 3 depende de D1/D2; Sprint 4 depende da Sprint 3 e de D3.
- Sprint 5 depende de D4; Sprint 6 de D5; Sprint 8 de D6.
- D6 pode ser resolvida com a KingHost em paralelo à Sprint 2.

## Estimativa (1 dev, sprints de 1 semana)

| Sprint | Esforço |
| --- | --- |
| 0 | 0,5 sem |
| 1 | 1 sem |
| 2 | 1–1,5 sem |
| 3 | 1–1,5 sem |
| 4 | 1 sem |
| 5 | 1,5–2 sem |
| 6 | 1 sem |
| 7 | 1 sem |
| 8 | 1 sem |

**Total: ~9–10,5 semanas.**

## Riscos principais

1. **Paridade com dados legados** — tipos inconsistentes (datas `varchar`, CEP
   `int`, charset misto). Mitigação: validação lado a lado (Sprint 7).
2. **Auth** — manter senhas bcrypt antigas válidas (D1) e não quebrar grupos.
3. **Volume de dados / performance** — usar `SQL_BIG_SELECTS`/índices e testar
   com massa real.
4. **Deploy/cutover** — risco de downtime; mitigar com backup e janela de baixo
   tráfego.
5. **Decisões em aberto (D1–D7)** — se atrasarem, travam sprints dependentes.

## Critérios de aceite gerais (Definition of Done)

- Paridade funcional com o CI3 (módulos em uso em `02`).
- Banco sem migração de dados; credenciais fora do repositório.
- Ambiente local sobe com `docker compose up`.
- CSRF, sessão segura e filters de autorização centralizados.
- Testes automatizados (mínimo) e validação lado a lado.
- Produção com HTTPS, backups e monitoramento.

