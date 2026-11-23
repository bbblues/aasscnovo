<?php

namespace App\Controllers\Concerns;

/**
 * Lógica compartilhada entre Pessoas e Solicitacoes para manipular o cadastro
 * de pessoa (normalização, validação e dados dos formulários).
 *
 * Os controllers que usam esta trait devem declarar as propriedades
 * `$usuarios`, `$cidades` e `$procedimentos`.
 */
trait ManipulaPessoa
{
    /**
     * Opções comuns aos formulários de pessoa e solicitação.
     *
     * @return array{clientes: list<array<string, mixed>>, agentes: list<array<string, mixed>>, cidades: list<array<string, mixed>>, procedimentos: list<array<string, mixed>>}
     */
    protected function dadosFormularioPessoa(): array
    {
        return [
            'clientes'      => $this->usuarios->doGrupo(4),
            'agentes'       => $this->usuarios->doGrupo(2),
            'cidades'       => $this->cidades->orderBy('cinome', 'ASC')->findAll(),
            'procedimentos' => $this->procedimentos->orderBy('med_nome', 'ASC')->findAll(),
        ];
    }

    /**
     * Normaliza os campos de pessoa antes de gravar.
     *
     * Decisão D4 (docs/decisoes.md): `uc`, `pes_user` e `pes_catpessoa` não são
     * expostos no formulário. Em novo cadastro: `uc = ''`, `pes_user` = usuário
     * logado e `pes_catpessoa = 0` (sem categoria). Na edição, preserva os
     * valores existentes.
     *
     * @param array<string, mixed> $pessoa
     *
     * @return array<string, mixed>
     */
    protected function normalizarPessoa(array $pessoa, bool $novo): array
    {
        if ($novo) {
            $pessoa['uc']            = '';
            $pessoa['pes_user']      = (int) session()->get('user_id');
            $pessoa['pes_catpessoa'] = 0;

            if (empty($pessoa['pes_pub'])) {
                $pessoa['pes_pub'] = date('Y-m-d');
            }
        }

        // CEP: o legado armazena como int; mantém apenas dígitos.
        if (! empty($pessoa['cep'])) {
            $pessoa['cep'] = (int) preg_replace('/\D/', '', (string) $pessoa['cep']);
        } else {
            unset($pessoa['cep']);
        }

        // Campos opcionais vazios viram null (omitidos do insert/update).
        foreach (['sexo', 'nascimento'] as $campo) {
            if (($pessoa[$campo] ?? '') === '') {
                unset($pessoa[$campo]);
            }
        }

        return $pessoa;
    }

    /**
     * Regras de validação dos campos de pessoa.
     *
     * @return array<string, string>
     */
    protected function regrasPessoa(): array
    {
        return [
            'pes_nome'    => 'required|max_length[50]',
            'pes_cidade'  => 'permit_empty|max_length[25]',
            'endereco'    => 'permit_empty|max_length[92]',
            'cep'         => 'permit_empty|max_length[9]',
            'nascimento'  => 'permit_empty|max_length[34]',
            'rg'          => 'permit_empty|max_length[14]',
            'cpf'         => 'permit_empty|max_length[16]',
            'sus'         => 'permit_empty|max_length[20]',
            'mae'         => 'permit_empty|max_length[45]',
            'email'       => 'required|valid_email|max_length[100]',
            'sexo'        => 'permit_empty|in_list[Masculino,Feminino,outro]',
            'pes_cliente' => 'permit_empty',
        ];
    }

    /**
     * Converte um valor de POST opcional em int ou null.
     */
    protected function nullableInt($valor): ?int
    {
        $valor = trim((string) $valor);

        return $valor === '' ? null : (int) $valor;
    }

    /**
     * Lê os filtros do formulário de listagem (POST).
     *
     * @return array{responsavel: string, cliente: string, pessoa: string, municipio: string, data_inicio: string, data_fim: string}
     */
    protected function lerFiltrosDoPost(): array
    {
        return [
            'responsavel' => trim((string) $this->request->getPost('responsavel')),
            'cliente'     => trim((string) $this->request->getPost('cliente')),
            'pessoa'      => trim((string) $this->request->getPost('pessoa')),
            'municipio'   => trim((string) $this->request->getPost('municipio')),
            'data_inicio' => trim((string) $this->request->getPost('data_inicio')),
            'data_fim'    => trim((string) $this->request->getPost('data_fim')),
        ];
    }
}
