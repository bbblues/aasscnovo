# 08 — Legado e Dívida Técnica

## Código morto / órfão (descartar na reescrita)

### Controllers
- `Procedimentos_.php` — duplicado de `Procedimentos.php` (descartar).

### Views órfãs (sem método correspondente nos controllers atuais)
- `solicitacoes/angelina.php`, `cardiacos.php`, `curitibanos.php`, `eneida.php`,
  `graopara.php`, `pescaria.php`, `saojose.php`, `teste.php` — aparentemente
  relatórios/visões específicas antigas.
- `solicitacoes/ver.php` — placeholder estático (não usado).
- `solicitacoes/parlamentar.php`, `municipio.php`, `cidade.php`,
  `etiquetas.php`, `etiquetas-parlamentar.php`, `lista-parlamentar.php` —
  visões antigas de "parlamentar" (algumas usam Materialize/jQuery UI).
- `views/pessoa/*` (add, edit, index) — versão antiga de pessoas (singular),
  substituída por `views/pessoas/*`.

### Assets legados (não usados no front atual)
- `assets/css/materialize*.css`, `assets/css/jquery-ui.css`
- `assets/js/materialize*.js`, `assets/js/jquery-ui.min.js`
- `assets/fonts/roboto/*`
- Views de erro CLI/HTML padrão do CI3 (substituídas pelas do CI4).

### Tabelas legadas (não usadas)
- `node`, `field_data_comment_body` (Drupal).

## Dívidas / riscos identificados (corrigir no CI4)

1. **Credenciais hardcoded** no `application/config/database.php` (produção e teste).
   → ir para `.env`, fora do repositório.
2. **CSRF desabilitado** (`csrf_protection = FALSE`). → habilitar no CI4.
3. **Acesso a `$_SESSION` diretamente** em controllers/views (ex.:
   `$_SESSION['user_id']`). → usar session service do CI4.
4. **Situação duplicada**: `solicitacoes.situacao` (texto) e `sol_sit_id` (FK),
   com mapeamento em PHP. → usar `situacoes` como lookup, mantendo o schema.
5. **Checagem de auth repetida** em cada construtor. → usar Filters do CI4.
6. **Login attempts / lockout desativados**. → habilitar proteção.
7. **Recuperação de senha sem expiração** (`forgot_password_expiration = 0`) e
   sem envio real de e-mail (`use_ci_email = FALSE`). → fluxo seguro no CI4.
8. **Concatenção de SQL**: `sol_por_cliente($dias)` interpola `$dias` na query
   (`INTERVAL ... DAY`). Hoje o valor é fixo (365), mas deve virar binding no CI4.
9. **`$config['language'] = 'english'`** com interface em PT-BR hardcoded nas
   views. → padronizar textos em PT-BR (ou i18n) no CI4.
10. **Tipos de dados inconsistentes** (datas como `varchar`, CEP como `int`,
    charset misto latin1/utf8). → manter por compatibilidade; documentado em
    `09-estrategia-de-banco.md`.
11. **Filtros persistem em sessão** (comportamento intencional, mas documentar).

## Decisões de descarte (resumo)
- Não portar: views órfãs, assets Materialize/jQuery UI, `Procedimentos_.php`,
  tabelas Drupal não usadas.
- Portar/reimplementar: tudo listado em `02-requisitos-funcionais.md`.
