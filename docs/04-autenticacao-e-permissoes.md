# 04 — Autenticação e Permissões

## Estado atual (Ion Auth 2.5.2 no CI3)

Configuração relevante (`application/config/ion_auth.php`):

| Item | Valor |
| --- | --- |
| identity | `email` |
| hash_method | `bcrypt` |
| default_rounds | 8 |
| salt_prefix | `$2y$` |
| min/max password | 8 / 20 |
| email_activation | FALSE |
| manual_activation | FALSE |
| remember_users | TRUE |
| track_login_attempts | FALSE |
| tables | `users`, `groups`, `users_groups`, `login_attempts` |

Senhas armazenadas com bcrypt (`password` varchar 255). Login usa `email` +
`password`. O controller `Auth` é o exemplo completo do Ion Auth (com
CSRF próprio via nonce em flashdata).

## Grupos e acesso atual

| Grupo (id) | O que pode |
| --- | --- |
| admin (1) | Tudo, inclusive `/auth` (gestão de usuários/grupos) |
| agente (2) | Dashboard, pessoas, solicitações, comentários, relatórios |
| cliente (4) | (aparece como "dono" das pessoas; acesso funcional a confirmar) |

Proteção atual: nos construtores, `if (session user_id == "") redirect('auth/login')`.
Não há filtro centralizado por grupo nas telas de negócio (apenas `/auth`
exige admin). **Isso deve ser melhorado no CI4.**

## Substituição no CI4

Ion Auth não tem versão oficial para CI4. Opções:

1. **Shield** (auth oficial do CodeIgniter 4) — recomendada para projeto novo;
   bem mantida, com grupos/permissões, "remember me", ações de senha.
   - **Ponto de atenção:** o schema do Shield é diferente do Ion Auth
     (`users`, `auth_identities`, `auth_groups_users`, etc.). Como decidimos
     **não migrar o banco**, seria necessário um adaptador ou importar os
     usuários para o novo schema (migração de dados de `users`/`users_groups`).
2. **Ports comunitários do Ion Auth para CI4** (ex.: `lonnieezell/ion-auth-4` ou
   similares) — mantêm schema parecido, reduzindo a fricção com os dados atuais.
3. **Auth customizada simples** com `Filter` do CI4 + `bcrypt` (PHP `password_hash`)
   sobre as tabelas atuais (`users`/`users_groups`/`groups`) — máximo controle e
   compatibilidade total com o banco, porém exige implementar login/logout,
   recuperação de senha e gestão de usuários.

> Recomendação preliminar: **opção 3 (auth customizada)** ou **opção 2 (port de
> Ion Auth)** para preservar o schema e os dados de usuários sem migração. A
> opção 1 (Shield) é a mais "padrão" mas exigiria migrar os usuários. Decidir
> antes da implementação.

## Requisitos de segurança para o CI4 (melhorias em relação ao atual)

- **CSRF habilitado** (hoje `csrf_protection = FALSE`).
- **Sessão segura**: cookies `HttpOnly` e `Secure` (HTTPS em produção).
- Credenciais no `.env` (hoje hardcoded).
- **Filtros de autorização por grupo** centralizados (não repetir checagem em
  cada construtor).
- **Rate limiting / bloqueio** de tentativas de login (hoje desabilitado).
- Fluxo de recuperação de senha **seguro** (token com expiração; hoje
  `forgot_password_expiration = 0`).
- Senha: manter bcrypt (preferir `password_hash`/`password_verify` do PHP).

## Regras de autorização propostas

| Rota/área | admin | agente | cliente |
| --- | --- | --- | --- |
| `/auth` (gestão usuários/grupos) | sim | não | não |
| Dashboard, pessoas, solicitações, comentários, relatórios | sim | sim | a confirmar |
| Login/logout/troca de senha | sim | sim | sim |
