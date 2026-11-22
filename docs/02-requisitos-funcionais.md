# 02 — Requisitos Funcionais

Módulos atualmente em uso. Base: análise dos controllers, models e views do CI3.

## 1. Autenticação (controller `Auth`)
- Login por **e-mail** (`identity = email`) + senha.
- Logout.
- Troca de senha (usuário logado).
- Esqueci minha senha / reset por código.
- Gestão de usuários (admin): listar, criar, editar, ativar/desativar.
- Gestão de grupos (admin): criar/editar.
- Regras de acesso por grupo (ver `04-autenticacao-e-permissoes.md`).

## 2. Dashboard (controller `Welcome`)
- Tabela "Suas solicitações em aberto" (agente logado, `sol_sit_id != 3`).
- Gráficos (Chart.js): ver `06-relatorios-e-dashboard.md`.

## 3. Pessoas
- **Listar** (`/pessoas`): filtros por responsável, cliente, pessoa, município,
  período (data início/fim) + paginação (20 por página).
  - Colunas: Data (`pes_pub`), Nome, Cidade, Cliente (`username`), Email, Telefone.
  - Filtros ficam na **sessão** (persistem entre navegações; botão "Limpar").
- **Editar** (`pessoas/edit/{id}`): atualiza pessoa + telefone; redireciona para
  `solicitacoes/edit/{id}` com flash de sucesso.
- **Cadastrar** (junto com solicitação, ver `solicitacoes/add_pessoa`).

### Campos de pessoa (formulário)
Nome, Cidade (select), Endereço, CEP, Telefone, Data de Nascimento, RG, CPF, SUS,
Nome da Mãe, Email, Sexo (Masculino/Feminino/outro), Cliente (select).

## 4. Solicitações
- **Lista geral** (`/solicitacoes`): mesmos filtros de pessoas + paginação.
  - Colunas: Data (`criado`), Pessoa, Pedido, Procedimento, Responsável, Cidade,
    Cliente, Situação, ação (abrir).
- **Em aberto** (`/solicitacoes/abertas`): `sol_sit_id != 3`.
- **Editar** (`/solicitacoes/edit/{pes_id}`): tela principal de atendimento.
  - Painel esquerdo: edição do cadastro da pessoa.
  - Painel direito: accordion com cada solicitação da pessoa (pedido + data no
    cabeçalho), contendo:
    - Agente (select), Situação (select), Pedido, Procedimento (select),
      Responsável, Observação.
    - Lista de comentários/atualizações daquela solicitação (com edição
      inline, somente pelo autor — campo `disabled` para outros).
    - Formulário "novo comentário".
    - Accordion extra "Cadastrar Nova Solicitação".
- **Atualizar solicitação** (`/solicitacoes/update/{sid}`): grava dados e
  redireciona para `edit/{spessoa}`.
- **Cadastrar solicitação** (`/solicitacoes/add`): grava e redireciona.
- **Cadastrar pessoa (+ solicitação opcional)** (`/solicitacoes/add_pessoa`):
  - Grava pessoa, telefone e (se `pedido` preenchido) solicitação.
  - Usa `separa_post()` para dividir o POST em pessoa/telefone/solicitação.

### Campos de solicitação (formulário)
Agente (select), Situação (select 1/2/3), Pedido, Procedimento (select),
Responsável, Observação.

## 5. Comentários / Atualizações
- **Listar** (`/coments`): filtros idem + paginação.
  - Colunas: avatar, Data, Pessoa, Pedido, Atualização (texto).
  - Avatar: `assets/img/avatares/{simple_lower(name)}.jpg`.
- **Adicionar** (`/coments/add/{pessoa}`): grava `name` (username), `created`,
  `changed`, `nid` (sid), `uid`.
- **Editar** (`/coments/update/{cid}`): atualiza `subject` e `changed`.

## 6. Relatórios
- **Relatório de atividades** (`/relatorios`): combina comentários + aberturas
  de solicitações, ordenado por data; filtros idem.
  - Colunas: Data, Nome, Pedido, Atividade.

## 7. Cadastros auxiliares (CRUDigniter — hoje não expostos no menu)
- **Cidades** (`/cidades`): listar, adicionar, editar, remover.
- **Procedimentos** (`/procedimentos`): listar, adicionar, editar, remover.
- **Catpessoas** (`/catpessoas`): listar, adicionar, editar, remover.

> Nota: esses três módulos não aparecem no menu atual. Decidir na reescrita se
> devem ganhar telas visíveis ou serem mantidos apenas como base de dados
> (cidades e procedimentos são usados como selects em solicitações/pessoas).

## Regras de negócio relevantes
- Mapeamento de situação (PHP): 1 = "Aguardando Resp", 2 = "Em Andamento",
  3 = "Finalizado" (`situacao_by_id()`).
- `situacao` (texto) é derivada de `sol_sit_id` no momento da gravação.
- Ao inserir solicitação: `criado = date('Y-m-d')` e `created = UNIX_TIMESTAMP()`.
- Filtros de período: em solicitações/pessoas usam `criado`/`pes_pub`; em
  comentários usam `changed` (com `data_final()` somando +1 dia no fim).
- `SET SQL_BIG_SELECTS=1` em todos os Models de consulta.
