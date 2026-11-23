# 15 — Revisão de Segurança (Sprint 7)

> Resultado da revisão de segurança prevista na Sprint 7. Cada item confere o
> requisito correspondente em `04-autenticacao-e-permissoes.md` e
> `13-plano-de-sprints.md`.

## Itens verificados (sem risco crítico aberto)

### 1. CSRF habilitado

- **Status:** ✅ ok.
- **Onde:** `app/Config/Security.php` (`$csrfProtection = 'cookie'`) e
  `app/Config/Filters.php` (`'csrf'` em `$globals['before']`).
- O filtro CSRF vale para todas as rotas, inclusive as públicas (`auth/login`,
  `auth/forgot_password`, `auth/reset_password/*`).
- Teste automatizado: `tests/unit/SegurancaConfigTest::testCsrfHabilitadoGlobalmente`.

### 2. Binding SQL (`INTERVAL $dias` → binding)

- **Status:** ✅ ok.
- **Onde:** `app/Models/Solicitacao.php` (`solPorCliente`, `solPorSituacao`).
- A interpolação legada (`INTERVAL $dias DAY`) foi substituída por
  `INTERVAL ? DAY` com `$this->db->query($sql, [$dias])`, eliminando o risco de
  SQL injection nesse ponto.
- Todas as demais consultas usam o Query Builder do CI4, que escapa valores por
  padrão.

### 3. Credenciais no `.env`

- **Status:** ✅ ok.
- **Onde:** `app/Config/Database.php` lê de variáveis de ambiente;
  `.env.example` documenta os valores do Docker.
- `.env` está listado no `.gitignore` (não é versionado). Não há credencial
  hardcoded no código.

### 4. Sessão e cookies seguros

- **Status:** ✅ ok.
- **Onde:** `app/Config/Cookie.php` — `$httponly = true`,
  `$secure = (ENVIRONMENT === 'production')`, `$samesite = 'Lax'`.
- Em produção (`CI_ENVIRONMENT=production`) os cookies passam a ser `Secure`
  automaticamente (necessário com HTTPS no deploy da Sprint 8).

### 5. Rate limiting / bloqueio de login

- **Status:** ✅ ok.
- **Onde:** `app/Libraries/Autenticacao.php` — `TENTATIVAS_MAXIMAS = 5`,
  `JANELA_MINUTOS = 15`, `EXPIRACAO_RESET = 3600` (token de recuperação expira).
- O `Auth::login` consulta `bloqueado()`, registra `registrarTentativa()` em
  falha e limpa em sucesso.
- Teste automatizado:
  `tests/database/AutenticacaoTest::testBloqueioAposLimiteDeTentativas`.

### 6. Senhas em bcrypt

- **Status:** ✅ ok (compatível com o legado).
- `password_hash`/`password_verify` em `app/Libraries/Autenticacao.php` e
  `app/Controllers/Auth.php`. Senhas bcrypt antigas continuam válidas.

## Itens adicionais (recomendações de endurecimento, não bloqueantes)

1. **Logout via GET** (`auth/logout`) — funcional, porém tecnicamente um logout
   pode ser disparado por CSRF. Impacto baixo (apenas encerra a sessão).
   Recomendação opcional: transformar em POST.
2. **`secureheaders`** (filtro do CI4) está comentado em `app/Config/Filters.php`
   (`after`). Habilitar adicionaria headers como `X-Content-Type-Options:
   nosniff` e `X-Frame-Options`. Recomendado habilitar junto com o deploy HTTPS
   (Sprint 8) e validar que não afeta o dashboard (Chart.js via CDN).

## Conclusão

Nenhum risco de segurança crítico aberto. Os requisitos de segurança da
reescrita (CSRF, SQL binding, credenciais em `.env`, sessão segura e rate
limiting) estão implementados e cobertos por testes.
