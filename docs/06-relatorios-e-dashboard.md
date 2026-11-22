# 06 — Relatórios e Dashboard

## Dashboard (`/`)

Título: "Suas solicitações em aberto".

### 1. Tabela de solicitações em aberto do agente
- Fonte: `Solicitacao->sol_por_agente_aberta(user_id)`.
- Filtro: `sagente = user logado` e `sol_sit_id != 3`.
- Colunas: Data, Pessoa, Pedido, Procedimento, Responsável, Cidade, Cliente,
  Situação, ação.

### 2. Gráficos (Chart.js via CDN)

#### Gráfico 1 (barras) — "% das solicitações abertas no último ano"
- Dados: `sol_por_cliente(365)` → transformado por `grafico_barras()` (percentual).
- Labels = usernames dos clientes; valores = percentuais.
- Regra de agrupamento: mais de 8 entradas → agrupa o restante em "outros".

#### Gráfico 2 (barras) — "Número de solicitações abertas no último ano"
- Dados: `sol_por_cliente(365)` → `grafico_barras_absoluto()` (valores absolutos).
- Mesmo agrupamento em "outros".

#### Gráfico 3 (doughnut) — "Solicitações por situação nos últimos 30 dias"
- Dados: `sol_por_situacao(30)` (agrupado por `situacao`).

> Observações de negócio:
> - `sol_por_cliente` exclui `users.id IN (76, 74)` do gráfico.
> - O código atual chama `sol_por_cliente(365)` duas vezes (ano e mês) usando o
>   mesmo período — aparentemente o rótulo "mês" está errado; **validar o período
>   desejado** na reescrita (ano vs mês).
> - `grafico_barras` e `grafico_barras_absoluto` são funções do helper
>   `funcoes_helper.php`.

## Relatório de Atividades (`/relatorios`)

- Título "Relatorio de atividades".
- Filtros idênticos às demais listagens.
- Dados: `Coment->ultimas_atualizacoes(0, 30, filters)` (comentários) +
  `Solicitacao->solicitacoes(0, 30, filters)` convertidos em "Abertura de
  solicitação".
- Mescla e ordena por `created` (via `usort`).
- Colunas: Data (`d/m/Y`), Nome, Pedido, Atividade.

## Helpers gráficos (funções a portar para CI4)
- `grafico_barras($array)` → percentuais, ordena desc, agrupa em "outros" (>8).
- `grafico_barras_absoluto($array)` → valores absolutos, idem agrupamento.
- `config_paginator($total, $url)` → config de paginação (20/pág, links Bootstrap).
- `situacao_by_id($id)` → 1/2/3 → texto.
- `data_final($data)` → +1 dia.
- `remover_valores_vazios(&$array)` → remove filtros vazios.
- `separa_post($p)` → divide POST em solicitação/pessoa/telefone.
- `simple_lower($texto)` → normaliza nome para nome de arquivo de avatar.
