# 14 — Validação lado a lado (Sprint 7)

> Roteiro para comparar o sistema novo (CI4) com o sistema atual (CI3) usando
> os **mesmos dados** e confirmando **os mesmos resultados** antes do deploy.
>
> Fonte de dados: importar o banco legado (`docs/_referencia/aassc.sql`) e/ou
> apontar os dois sistemas para a mesma base (ou uma cópia dela).

## Pré-requisitos

- [ ] Banco legado acessível (ou cópia fiel) com dados reais.
- [ ] Aplicação CI4 rodando (`docker compose up`) apontando para esse banco.
- [ ] Acesso ao sistema CI3 para comparação (ou exportação dos resultados).

## Checklist por área

### Dashboard (`/`)

- [ ] Tabela "Suas solicitações em aberto" bate com o CI3 (mesmo agente logado,
      `sol_sit_id != 3`).
- [ ] Gráfico "% das solicitações abertas no último ano" (por cliente) — mesmos
      percentuais e agrupamento em "outros" (>8).
- [ ] Gráfico "Número de solicitações abertas no último ano" — mesmos valores.
- [ ] Gráfico "Solicitações por situação nos últimos 30 dias" — mesmas contagens.
- [ ] Exclusão dos usuários de sistema (ids 76 e 74) conferida.

### Pessoas (`/pessoas`)

- [ ] Listagem ordenada por `TRIM(pes_nome)` idêntica ao CI3.
- [ ] Filtros (responsável, cliente, pessoa, município, período) retornam os
      mesmos registros.
- [ ] Filtros persistem em sessão; "Limpar" zera.
- [ ] Paginação de 20 registros por página idêntica.
- [ ] Edição de pessoa atualiza pessoa + telefone como no CI3.

### Solicitações (`/solicitacoes` e `/solicitacoes/abertas`)

- [ ] Lista geral e "em aberto" retornam os mesmos registros (mesma ordenação).
- [ ] Cadastro de solicitação grava `situacao` derivada de `sol_sit_id`,
      `criado = date('Y-m-d')` e `created = UNIX_TIMESTAMP()` como no CI3.
- [ ] Edição de solicitação atualiza os mesmos campos.
- [ ] Cadastro de pessoa com solicitação opcional (`add_pessoa`) funciona.

### Comentários (`/coments`)

- [ ] Listagem idêntica (avatar via `simple_lower(name)`).
- [ ] Adição de comentário grava `created`/`changed` como timestamp.
- [ ] Edição inline restrita ao autor.

### Relatório (`/relatorios`)

- [ ] Mescla de comentários + aberturas ordenada por `created` desc.
- [ ] Filtros idênticos aos das listagens.
- [ ] Formato de data `d/m/Y` conferido.

### Autenticação

- [ ] Login com senha bcrypt antiga funciona (sem re-hash forçado).
- [ ] Grupos/permissões conferidos (admin/agente/cliente).
- [ ] Rate limiting: 5 tentativas falhas bloqueiam por 15 min.
- [ ] Troca e recuperação de senha funcionam.

## Critérios de aceite (Sprint 7)

- [ ] Resultados batem com o CI3 nas áreas acima.
- [ ] Nenhuma divergência de dados/ordenação/formatação de datas.
- [ ] Nenhum risco de segurança crítico (ver `15-revisao-de-seguranca.md`).

## Observações de paridade de dados

- Datas: listagens/dashboard em `d/m/y`; relatório em `d/m/Y`.
- CEP armazenado como `int` (apenas dígitos).
- `users.active` pode ser `NULL` (tratado como ativo).
- `solicitacoes.created` é timestamp unix em `varchar(20)`; `criado` é `DATE`.
