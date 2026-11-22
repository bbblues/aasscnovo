# 12 — Roadmap de Implementação

## Fase 0 — Preparação
- [x] Analisar aplicação CI3 e extrair especificações (esta pasta).
- [ ] Fazer backup definitivo do projeto CI3 (Git/zip) antes de apagar.
- [ ] Confirmar decisões em aberto:
  - Auth no CI4 (custom vs Ion Auth port vs Shield) — ver `04`.
  - Nomes reais dos grupos (`groups`), grupo 3, acesso do "cliente".
  - Período correto dos gráficos (ano vs mês) — ver `06`.
  - Uso de `uc`, `pes_user`, `pes_catpessoa` em `pessoas`.
  - Banco em produção: container MariaDB vs banco existente da KingHost.

## Fase 1 — Bootstrap do projeto
- [ ] Criar projeto CI4 (appstarter via Composer).
- [ ] Docker local (`docker-compose.yml`, Dockerfile) — ver `10`.
- [ ] `.env` / `.env.example`; `.gitignore`.
- [ ] Subir e validar "Hello World" em `http://localhost:8080`.

## Fase 2 — Banco e camada de dados
- [ ] Migrações espelhando o schema atual — ver `09`.
- [ ] Models: `Pessoa`, `Solicitacao`, `Comentario`, `Telefone`, `Usuario`,
      `Cidade`, `Procedimento`, `CategoriaPessoa`, `Situacao`.
- [ ] Helpers: `funcoes` (paginador, gráficos, situação, separa_post, etc.).
- [ ] Índices aditivos (se aprovado).

## Fase 3 — Autenticação e autorização
- [ ] Implementar login/logout/recuperação (conforme decisão da Fase 0).
- [ ] Filters de auth e por grupo.
- [ ] CSRF + sessão segura.
- [ ] Gestão de usuários/grupos (admin).

## Fase 4 — Funcionalidades
- [ ] Layout base (menu/topo/rodapé + CSS próprio).
- [ ] Dashboard + gráficos.
- [ ] Pessoas (listar/filtrar/editar/cadastrar).
- [ ] Solicitações (listar, em aberto, editar, cadastrar, add_pessoa).
- [ ] Comentários (listar, adicionar, editar).
- [ ] Relatório de atividades.
- [ ] CRUDs auxiliares (cidades, procedimentos, catpessoas) — conforme decisão.

## Fase 5 — Qualidade e validação
- [ ] Testes automatizados (mínimo: Models + fluxos críticos).
- [ ] Validação lado a lado com o sistema atual (mesmos dados, mesmos resultados).
- [ ] Revisão de segurança (CSRF, senha, SQL binding, credenciais).

## Fase 6 — Deploy
- [ ] Provisionar VPS KingHost — ver `11`.
- [ ] DNS + HTTPS (Let's Encrypt).
- [ ] Deploy de produção, backups e monitoramento.
- [ ] Cutover (trocar `sis.aassc.com.br` para o novo sistema).

## Critérios de aceite (resumo)
- Paridade funcional com o CI3 (módulos em uso), banco sem migração de dados,
  ambiente local via `docker compose up`, produção estável com HTTPS e backups.
