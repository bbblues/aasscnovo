# 09 — Estratégia de Banco de Dados

## Decisão

**Manter o schema atual compatível** (tabelas, nomes de colunas e tipos), por
causa do **grande volume de dados** existente. Não renomear nem redesenhar.
Aplicar apenas **melhorias aditivas e seguras**.

## Como o CI4 vai lidar com o banco

1. **Migrações que espelham o schema atual** (`app/Database/Migrations`):
   recriar as mesmas tabelas/colunas em código, para que o projeto seja
   reproduzível em qualquer ambiente (dev/teste) sem depender do dump manual.
   - Para produção, **não rodar migrações que recriam tabelas existentes**
     com dados; as migrações servem para ambientes novos.
2. **Models mapeiam tabelas legadas** com nomes claros:
   - `Pessoa` → tabela `pessoas`
   - `Solicitacao` → tabela `solicitacoes`
   - `Comentario` → tabela `comment`
   - `Telefone` → tabela `telefones`
   - `Usuario`/`User` → tabela `users` (+ `users_groups`, `groups`)
   - `Cidade` → `cidades`, `Procedimento` → `procedimentos`,
     `CategoriaPessoa` → `catpessoa`, `Situacao` → `situacoes`
   - Definir em cada Model: `$table`, `$primaryKey`, `$returnType`,
     `$allowedFields` e (opcionalmente) `$useTimestamps = false`.

## Melhorias aditivas recomendadas (não mexem nos dados)

- **Índices** para performance em grandes volumes:
  - `solicitacoes.spessoa`, `solicitacoes.sagente`, `solicitacoes.sol_sit_id`,
    `solicitacoes.criado`
  - `pessoas.pes_cliente`, `pessoas.pes_pub`
  - `comment.nid`, `comment.changed`
  - `telefones.entity_id` (já existe `idx_entity_id`)
- **Charset/collation → utf8mb4**: tarefa **separada e planejada** (em janela
  de baixo tráfego), pois `ALTER ... CONVERT TO CHARACTER SET` pode travar
  tabelas grandes. Hoje há mistura (`catpessoa` em latin1, restante em utf8).
- **Credenciais via `.env`** (`database.default.hostname`, etc.).

## Pontos de atenção de compatibilidade

- `SET SQL_BIG_SELECTS=1` usado hoje em todos os Models de consulta. No CI4,
  executar o mesmo statement na conexão (ou garantir índices adequados).
- `users.password` já é bcrypt (`$2y$`, 8 rounds) — compatível com
  `password_verify()` do PHP.
- `solicitacoes.created` guarda timestamp unix como `varchar(20)`; `criado` é
  `DATE`. Manter ambos e usar consistentemente:
  - `criado` para exibição de data e filtros de período.
  - `created` para ordenação de atividade (junto com `comment.changed`).
- `pessoas.pes_cliente` é `varchar(3)` mas guarda o id do usuário. Manter o
  tipo; apenas tratar como inteiro no código.

## Migração de dados
- **Não é necessária** para o schema (mantido). Se optarmos por Shield/Ion Auth
  novo com schema diferente, será necessária uma migração **somente dos dados de
  usuários/grupos** (`users`, `users_groups`, `groups`), documentada à parte.

## Ambientes
- `development` (Docker local): banco MariaDB/MySQL em container, seed opcional.
- `production` (VPS KingHost): banco existente `aassc` (ou novo container com
  import do dump + dados).
