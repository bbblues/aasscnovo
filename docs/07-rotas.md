# 07 — Rotas (mapeamento CI3 → CI4)

No CI3 as rotas são implícitas (`controller/método/parâmetro`). No CI4 devem ser
declaradas em `app/Config/Routes.php` (ou via atributos nos controllers).

## Mapeamento completo

| Método | URL atual (CI3) | Ação |
| --- | --- | --- |
| GET | `/` | `Welcome::index` (dashboard) |
| GET/POST | `/pessoas` | `Pessoas::index` (listar) |
| GET | `/pessoas/limpar` | limpar filtros |
| POST | `/pessoas/edit/{id}` | `Pessoas::edit` |
| GET/POST | `/solicitacoes` | `Solicitacoes::index` |
| GET | `/solicitacoes/limpar` | limpar filtros |
| GET/POST | `/solicitacoes/edit/{pes_id}` | edição |
| POST | `/solicitacoes/update/{sid}` | atualizar |
| GET/POST | `/solicitacoes/add_pessoa` | cadastrar pessoa |
| POST | `/solicitacoes/add` | cadastrar solicitação |
| GET | `/solicitacoes/abertas` | em aberto |
| GET/POST | `/coments` | `Coments::index` |
| GET | `/coments/limpar` | limpar filtros |
| POST | `/coments/add/{pessoa}` | adicionar comentário |
| POST | `/coments/update/{cid}` | editar comentário |
| GET/POST | `/relatorios` | `Relatorios::index` |
| GET | `/cidades`, `/cidades/add`, `/cidades/edit/{cid}`, `/cidades/remove/{cid}` | CRUD cidades |
| GET | `/procedimentos`, `.../add`, `.../edit/{med_id}`, `.../remove/{med_id}` | CRUD procedimentos |
| GET | `/catpessoas`, `.../add`, `.../edit/{cp_id}`, `.../remove/{cp_id}` | CRUD catpessoas |
| GET/POST | `/auth/login`, `/auth/logout` | login/logout |
| GET/POST | `/auth` | lista usuários (admin) |
| GET/POST | `/auth/change_password` | trocar senha |
| GET/POST | `/auth/forgot_password` | esqueci senha |
| GET/POST | `/auth/reset_password/{code}` | reset senha |
| GET | `/auth/activate/{id}/{code}` | ativar |
| GET/POST | `/auth/deactivate/{id}` | desativar |
| GET/POST | `/auth/create_user` | criar usuário |
| GET/POST | `/auth/edit_user/{id}` | editar usuário |
| GET/POST | `/auth/create_group` | criar grupo |
| GET/POST | `/auth/edit_group/{id}` | editar grupo |

## Convenções a definir no CI4
- Namespace base: `App` (padrão CI4). Sugestão de namespaces: `App\Controllers`,
  `App\Models`, `App\Entities`, `App\Database\Migrations`, `App\Filters`,
  `App\Helpers`.
- Métodos HTTP explícitos (GET/POST) e proteção CSRF (exceto login? aplicar
  conforme padrão CI4).
- Filtros: `auth` (requer login) aplicado a todas as áreas exceto login/recuperação;
  `group:admin` para `/auth`.
- Manter URLs compatíveis onde possível (para preservar links/favoritos), mas
  é aceitável ajustar casos como `pessoas/edit` → manter.
