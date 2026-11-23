<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3">Editar pessoa</h1>

<div class="card">
    <div class="card-body">
        <?= form_open('pessoas/edit/' . $pessoa['pes_id'], ['onsubmit' => "return confirm('Atualizar cadastro?')"]) ?>
            <?= view('pessoas/_form_pessoa', [
                'pessoa'   => $pessoa,
                'telefone' => $telefone,
                'cidades'  => $cidades,
                'clientes' => $clientes,
            ]) ?>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('solicitacoes/edit/' . $pessoa['pes_id']) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
