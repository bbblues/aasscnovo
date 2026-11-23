# 01 — Modelo de Dados

> Fonte: `_referencia/aassc.sql` (dump de estrutura, sem dados).
> Decisão: **manter tabelas, colunas e tipos como estão** (compatibilidade com
> dados existentes). Não há chaves estrangeiras definidas; os relacionamentos
> são lógicos, feitos nos joins da aplicação.

## Tabelas

### `catpessoa` (categoria de pessoa)
| Coluna | Tipo | Observação |
| --- | --- | --- |
| cp_id | int(9) UNSIGNED | chave lógica — **sem PK/AUTO_INCREMENT no dump** |
| cp_nome | varchar(255) | nome da categoria |

> Charset `latin1` (inconsistente com o restante, que é `utf8`).

### `cidades`
| Coluna | Tipo | Observação |
| --- | --- | --- |
| cid | int(8) | PK, AUTO_INCREMENT |
| cinome | varchar(255) | nome da cidade |

### `comment` (comentários/atualizações — tabela reaproveitada do Drupal)
| Coluna | Tipo | Observação |
| --- | --- | --- |
| cid | int(11) | PK, AUTO_INCREMENT |
| nid | int(11) | `solicitacoes.sid` (solicitação) |
| uid | int(11) | `users.id` (autor do comentário) |
| subject | text | texto do comentário |
| created | int(11) | timestamp unix de criação |
| changed | int(11) | timestamp unix da última edição |
| name | varchar(60) | username do autor (redundante) |

### `groups` (Ion Auth)
| Coluna | Tipo |
| --- | --- |
| id | mediumint(8) UNSIGNED |
| name | varchar(20) |
| description | varchar(100) |

> Sem PK/AUTO_INCREMENT no dump (mantido por fidelidade ao banco original).

### `login_attempts` (Ion Auth)
| Coluna | Tipo |
| --- | --- |
| id | int(11) UNSIGNED |
| ip_address | varchar(15) |
| login | varchar(100) |
| time | int(11) UNSIGNED |

> Sem PK/AUTO_INCREMENT no dump (mantido por fidelidade ao banco original).

### `pessoas`
| Coluna | Tipo | Observação |
| --- | --- | --- |
| pes_id | int(8) UNSIGNED | PK, AUTO_INCREMENT |
| pes_nome | varchar(50) | nome |
| pes_cliente | varchar(3) | FK lógica → `users.id` (cliente, grupo 4) |
| pes_cidade | varchar(25) | cidade (texto livre) |
| sus | varchar(20) | número SUS |
| cpf | varchar(16) | CPF |
| nascimento | varchar(34) | data de nascimento (texto) |
| endereco | varchar(92) | endereço |
| mae | varchar(45) | nome da mãe |
| rg | varchar(14) | RG |
| email | varchar(100) NOT NULL | email |
| uc | varchar(255) NOT NULL | (contexto a confirmar) |
| sexo | varchar(9) | sexo |
| pes_user | int(3) | usuário que cadastrou (uso a confirmar) |
| pes_pub | date | data de publicação (default `curdate()`) |
| pes_catpessoa | int(9) NOT NULL | FK lógica → `catpessoa.cp_id` |
| cep | int(9) | CEP (numérico) |

### `procedimentos`
| Coluna | Tipo | Observação |
| --- | --- | --- |
| med_id | int(10) UNSIGNED | PK, AUTO_INCREMENT |
| med_nome | varchar(255) | nome do procedimento |

### `situacoes`
| Coluna | Tipo |
| --- | --- |
| sit_id | int(1) (PK) |
| sit_nome | varchar(50) |
| sit_icon | varchar(50) |
| sit_color | varchar(50) |

> Valores esperados (mapeados em PHP por `situacao_by_id`):
> 1 = Aguardando resposta, 2 = Em andamento, 3 = Finalizado.
> A tabela existe mas **não é usada** no código atual.

### `solicitacoes`
| Coluna | Tipo | Observação |
| --- | --- | --- |
| sid | int(9) UNSIGNED | PK, AUTO_INCREMENT |
| spessoa | int(9) | FK lógica → `pessoas.pes_id` |
| sagente | int(5) | FK lógica → `users.id` (agente, grupo 2) |
| obs | text | observações |
| pedido | text | descrição do pedido |
| procedimento | varchar(255) | nome do procedimento (texto) |
| responsavel | varchar(255) | responsável (texto livre) |
| situacao | varchar(255) | situação (texto) |
| sol_sit_id | int(1) UNSIGNED | FK lógica → `situacoes.sit_id` |
| created | varchar(20) | timestamp unix (gravado como string) |
| criado | date | data de criação (date) |

### `telefones`
| Coluna | Tipo | Observação |
| --- | --- | --- |
| entity_id | int(9) | FK lógica → `pessoas.pes_id` (índice `idx_entity_id`) |
| field_telefones_value | varchar(253) | número de telefone |

### `users` (Ion Auth)
| Coluna | Tipo | Observação |
| --- | --- | --- |
| id | int(11) UNSIGNED | PK, AUTO_INCREMENT |
| ip_address | varchar(45) | |
| username | varchar(100) | |
| password | varchar(255) | bcrypt |
| salt | varchar(255) | |
| email | varchar(100) | |
| activation_code | varchar(40) | |
| forgotten_password_code | varchar(40) | |
| forgotten_password_time | int(11) UNSIGNED | |
| remember_code | varchar(40) | |
| created_on | int(11) UNSIGNED | default 1268889823 |
| last_login | int(11) UNSIGNED | |
| active | tinyint(1) UNSIGNED | |
| first_name | varchar(50) | |
| last_name | varchar(50) | |
| company | varchar(100) | |
| phone | varchar(20) | |

### `users_groups` (Ion Auth)
| Coluna | Tipo |
| --- | --- |
| id | int(11) UNSIGNED (PK) |
| user_id | int(11) UNSIGNED |
| group_id | mediumint(8) UNSIGNED |

## Tabelas legadas NÃO usadas (Drupal)

- `node` — tabela base do Drupal; não usada pela aplicação.
- `field_data_comment_body` — campo do Drupal; não usado.

> `comment` é a única tabela Drupal reaproveitada (para armazenar comentários
> das solicitações). Manter o nome no novo projeto, mapeando via Model.

## Relacionamentos lógicos (joins usados)

```
solicitacoes.spessoa    -> pessoas.pes_id
solicitacoes.sagente    -> users.id        (agente, grupo 2)
pessoas.pes_cliente     -> users.id        (cliente, grupo 4)
solicitacoes.sol_sit_id -> situacoes.sit_id
comment.nid             -> solicitacoes.sid
comment.uid             -> users.id
telefones.entity_id     -> pessoas.pes_id
users_groups.user_id    -> users.id
users_groups.group_id   -> groups.id
```

## Consultas/modelos que dependem do schema (resumo)

- Listagem de **pessoas**: `pessoas p JOIN telefones ON entity_id=pes_id JOIN users u ON u.id=p.pes_cliente`, agrupado por `pes_id`, ordenado por `TRIM(pes_nome)`.
- Listagem de **solicitações**: `solicitacoes s JOIN pessoas p ON s.spessoa=p.pes_id JOIN users u ON u.id=p.pes_cliente`.
- **Atualizações/comentários**: `comment c JOIN solicitacoes s ON s.sid=c.nid JOIN pessoas p ON p.pes_id=s.spessoa`.
- Dashboard:
  - `sol_por_agente_aberta`: `solicitacoes JOIN pessoas JOIN users` onde `sagente = user logado` e `sol_sit_id != 3`.
  - `sol_por_cliente`: agrupa por `pes_cliente`, `criado >= DATE_SUB(CURRENT_DATE(), INTERVAL N DAY)`, exclui `users.id IN (76,74)`.
  - `sol_por_situacao`: agrupa por `situacao`, `criado >= DATE_SUB(CURRENT_DATE(), INTERVAL N DAY)`.

> Em todos os Models há `SET SQL_BIG_SELECTS=1` no construtor (importante para
> grandes volumes com joins agrupados). Considerar manter essa instrução ou
> usar índices apropriados no CI4.
