# Especificações — Sistema AASSC (reescrita para CodeIgniter 4)

Esta pasta contém a documentação de especificação para a reescrita do sistema
AASSC, que hoje está em **CodeIgniter 3**, para **CodeIgniter 4**.

> **Objetivo da pasta:** servir como fonte única de verdade para reconstruir a
> aplicação do zero. Todo o restante do projeto antigo será removido após o
> backup; apenas esta pasta permanecerá.

## Índice de documentos

| Arquivo | Conteúdo |
| --- | --- |
| [00-visao-geral.md](00-visao-geral.md) | O que é o sistema, usuários/perfis, stack atual e alvo |
| [01-modelo-de-dados.md](01-modelo-de-dados.md) | Tabelas, colunas, relacionamentos e peculiaridades |
| [02-requisitos-funcionais.md](02-requisitos-funcionais.md) | Funcionalidades, telas, filtros e comportamentos |
| [03-casos-de-uso.md](03-casos-de-uso.md) | Fluxos principais de uso |
| [04-autenticacao-e-permissoes.md](04-autenticacao-e-permissoes.md) | Auth atual (Ion Auth) e substituição no CI4 |
| [05-interfaces.md](05-interfaces.md) | Layout, telas, campos e componentes visuais |
| [06-relatorios-e-dashboard.md](06-relatorios-e-dashboard.md) | Dashboard, gráficos e relatório de atividades |
| [07-rotas.md](07-rotas.md) | Mapeamento de URLs (CI3 → CI4) |
| [08-legado-e-divida-tecnica.md](08-legado-e-divida-tecnica.md) | Código morto, débitos e decisões de descarte |
| [09-estrategia-de-banco.md](09-estrategia-de-banco.md) | Estratégia de banco (manutenção + melhorias aditivas) |
| [10-ambiente-docker.md](10-ambiente-docker.md) | Ambiente de desenvolvimento local com Docker |
| [11-deploy-vps-kinghost.md](11-deploy-vps-kinghost.md) | Deploy em VPS dedicada KingHost |
| [12-roadmap.md](12-roadmap.md) | Fases de implementação |
| [13-plano-de-sprints.md](13-plano-de-sprints.md) | Plano de desenvolvimento em sprints |
| [14-validacao-lado-a-lado.md](14-validacao-lado-a-lado.md) | Roteiro de validação lado a lado com o CI3 (Sprint 7) |
| [15-revisao-de-seguranca.md](15-revisao-de-seguranca.md) | Revisão de segurança da Sprint 7 |
| [16-deploy-producao.md](16-deploy-producao.md) | Runbook de deploy em produção (Sprint 8) |

## Referências (arquivos)

- `_referencia/aassc.sql` — cópia do schema do banco (somente estrutura).
- `_referencia/assets/` — assets reais preservados (ícone, `procurar.svg`, avatares).

## Decisões-chave já alinhadas

1. **Reescrever em CodeIgniter 4** a partir das especificações (não portar arquivo a arquivo).
2. **Manter o schema do banco compatível** (não renomear tabelas/colunas nem mudar tipos),
   por causa do grande volume de dados existente. Aplicar apenas melhorias aditivas seguras.
3. **Desenvolvimento local com Docker**.
4. **Produção em VPS dedicada KingHost**.
