# 03 — Casos de Uso

Fluxos principais (atores: **admin**, **agente**, **cliente**).

## UC-01: Login
1. Usuário acessa o sistema; é redirecionado para `/auth/login`.
2. Informa e-mail (identity) e senha.
3. Em caso de sucesso, vai para o dashboard (`/`).
4. Em caso de falha, exibe mensagem de erro.

## UC-02: Dashboard do agente
1. Agente logado acessa `/`.
2. Vê a tabela "Suas solicitações em aberto" (onde `sagente = seu id` e não finalizadas).
3. Vê os gráficos de distribuição (clientes e situação).

## UC-03: Cadastrar pessoa (+ solicitação opcional)
1. Menu Pessoas → "Cadastrar Pessoa" (`/solicitacoes/add_pessoa`).
2. Preenche dados pessoais e telefone.
3. Opcionalmente preenche a solicitação.
4. Submete → grava pessoa, telefone e (se houver pedido) solicitação.
5. Redireciona para `/solicitacoes/edit/{pes_id}` com flash "Cadastro realizado".

## UC-04: Atender/editar uma solicitação
1. A partir de listagem ou dashboard, clica em uma pessoa → `/solicitacoes/edit/{pes_id}`.
2. Abre o accordion da solicitação desejada.
3. Altera agente, situação, pedido, procedimento, responsável, observação.
4. Submete → grava e redireciona para a mesma tela com flash.
5. Pode adicionar um novo comentário ou editar um comentário próprio.

## UC-05: Cadastrar nova solicitação para pessoa existente
1. Em `/solicitacoes/edit/{pes_id}`, abre "Cadastrar Nova Solicitação".
2. Preenche agente, situação, pedido, procedimento, responsável, observação.
3. Submete → grava (`spessoa = pes_id`) e redireciona.

## UC-06: Filtrar listagens
1. Em Pessoas, Solicitações, Comentários ou Relatório.
2. Preenche filtros (responsável, cliente, pessoa, município, período).
3. Submete → filtros salvos na sessão; listagem/paginação refeita.
4. "Limpar" remove os filtros da sessão.

## UC-07: Relatório de atividades
1. Menu Relatórios → "Relatório de Atividades".
2. Aplica filtros opcionais.
3. Vê a lista consolidada (comentários + aberturas de solicitação) por data.

## UC-08: Gestão de usuários/grupos (admin)
1. Admin acessa `/auth` (lista de usuários).
2. Cria/edita usuários, define grupos, ativa/desativa.
3. Cria/edita grupos.

## UC-09: Recuperação de senha
1. Em `/auth/forgot_password`, informa o e-mail.
2. Recebe código (atualmente: `use_ci_email = FALSE`, retorna código na tela —
   **deve ser revisto no CI4 para envio real de e-mail ou fluxo seguro**).
3. Acessa `/auth/reset_password/{code}` e define nova senha.

## Observações de usabilidade a preservar
- O layout usa `container-fluid` na maioria das telas.
- Nomes de pessoas são ordenados por `TRIM(pes_nome)`.
- Datas exibidas no formato `d/m/y` (dashboard/listagens) ou `d/m/Y` (relatório).
- Confirmação antes de atualizar (`confirm()` em formulários de edição).
