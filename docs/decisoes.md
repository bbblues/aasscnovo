# Decisões de projeto (D1–D7)

> Registro das decisões listadas em `13-plano-de-sprints.md`.
> As demais decisões (D4–D7) permanecem abertas e serão registradas aqui
> quando fechadas.

## D1 — Auth no CI4

**Decisão:** autenticação **customizada (opção 3)** sobre as tabelas legadas
(`users`, `groups`, `users_groups`, `login_attempts`), usando `password_hash` /
`password_verify` (bcrypt).

**Justificativa:** preserva o schema e os dados de usuários sem migração,
mantendo compatibilidade com as senhas bcrypt já existentes. O Shield (opção 1)
exigiria migrar os dados; o port comunitário do Ion Auth (opção 2) adicionaria
dependência externa sem ganho claro.

## D2 — Grupos e acesso

**Grupos existentes** (conforme `04-autenticacao-e-permissoes.md`):

| id | name | acesso |
| --- | --- | --- |
| 1 | admin | tudo, inclusive `/auth` (gestão de usuários/grupos) |
| 2 | agente | áreas de negócio (dashboard, pessoas, solicitações, comentários, relatórios) |
| 4 | cliente | login/logout/troca de senha + áreas de negócio (acesso funcional idem agente por ora) |

**Decisões:**
- O **grupo 3** não tem papel definido; nenhuma regra específica foi criada
  para ele (não bloqueia a Sprint 3).
- O **cliente** mantém acesso às áreas de negócio (como no legado); a restrição
  por grupo, se necessária, será aplicada em sprint futura.
- Autorização centralizada em filters (`auth` global + `group:admin` para
  `/auth`), sem checagem repetida nos construtores dos controllers.

## D3 — Período dos gráficos (ano vs mês)

**Decisão:** manter os períodos descritos em `06-relatorios-e-dashboard.md`:

- Gráficos de barras (percentual e absoluto): **último ano** → `sol_por_cliente(365)`.
- Gráfico doughnut (por situação): **últimos 30 dias** → `sol_por_situacao(30)`.

**Justificativa:** o legado chamava `sol_por_cliente(365)` duas vezes usando rótulos
conflitantes ("ano" e "mês"). A especificação de reescrita já normaliza os dois
gráficos de barras para "último ano" (365 dias) e reserva o período mensal (30 dias)
para o gráfico de situação, eliminando a ambiguidade sem alterar o comportamento
efetivo das barras.

## Senha inicial do ambiente novo (seeder `AuthSeeder`)

- e-mail: `admin@aassc.com.br`
- senha: `admin123` (alterar após o primeiro login)

> O seeder cria os grupos 1/2/4 e o admin, pois o dump de referência possui
> apenas estrutura (sem dados).
