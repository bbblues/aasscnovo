# 05 — Interfaces (Layout e Telas)

## Estrutura de layout

- Arquivos base: `topo.php` (abre HTML + `<head>` + menu + abre container),
  `base.php` (fecha container + Bootstrap JS + fecha HTML), `menu.php`.
- `<title>Sistema AASSC</title>`, favicon `assets/aassc-icon.png`.
- Bootstrap 5.3.3 via CDN; CSS customizado inline (`views/css.php`) — no CI4
  deve virar um `.css` estático em `public/assets/css/`.
- Cor primária: `#17a2b8` (teal/ciano). Fundo `#EAEAEA`, fonte base `0.8rem`.
- Container: `container-fluid` na maioria das telas (`$container`).

## Menu (navbar)
- Marca: ícone + "Sistema AASSC".
- Dropdown **Pessoas**: "Listar Pessoas" (`/pessoas`), "Cadastrar Pessoa"
  (`/solicitacoes/add_pessoa`).
- Dropdown **Solicitações**: "Lista Geral" (`/solicitacoes`),
  "Solicitações em Aberto" (`/solicitacoes/abertas`).
- Dropdown **Relatórios**: "Relatório de Atividades" (`/relatorios`).
- Campo de busca "Buscar pessoa" (envia `pes_nome` para `/pessoas`).

## Classes CSS relevantes (reaproveitar)
- `.tabela-dashboard`, `.titulo_dashboard`, `.form_topo`, `.edicao`,
  `.nao-quebrar-linha`, `.detalhes`, `.avatar`, `.ver_tudo`, paginação.
- Formulários de filtro no topo (`.form_topo`); formulários de edição (`.edicao`).

## Telas

### Login / auth
- Formulário de login (identity/password + "remember").
- Telas do Ion Auth (trocar senha, esqueci, reset, criar/editar usuário, grupos).

### Dashboard (`/`)
- Título "Suas solicitações em aberto" + tabela.
- Linha com 3 gráficos (2 barras + 1 doughnut) — ver `06`.

### Listagens (pessoas, solicitações, comentários)
- Título + formulário de filtros (row `.form_topo`) com: Responsável (texto),
  Cliente (select), Pessoa (texto), Município (texto), Data início (date),
  Data fim (date), botões Filtrar/Limpar.
- Tabela `.tabela-dashboard` com colunas conforme `02-requisitos-funcionais.md`.
- Paginação centralizada (20 por página).

### Edição de solicitação (`/solicitacoes/edit/{pes_id}`)
- Layout em 2 colunas (`row justify-content-around edicao`):
  - **col-md-4**: formulário "Atualizar cadastro da pessoa" (todos os campos de pessoa).
  - **col-md-6**: accordion de solicitações.
- Dentro de cada solicitação: formulário de edição + comentários + novo comentário.
- Accordion "Cadastrar Nova Solicitação".

### Relatório de atividades
- Título + filtros + tabela (Data, Nome, Pedido, Atividade).

## Referência de assets
- `assets/aassc-icon.png` — favicon/logo.
- `assets/img/procurar.svg` — ícone de ação (abrir).
- `assets/img/avatares/{nome}.jpg` — avatares (adriana, caio, joo, naiara, noemi).

> No CI4, assets vão para `public/assets/` e são referenciados via `base_url()`.
