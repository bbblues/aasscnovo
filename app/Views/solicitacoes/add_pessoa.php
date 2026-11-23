<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3">Cadastrar pessoa</h1>

<div class="card">
    <div class="card-body">
        <?= form_open('solicitacoes/add_pessoa') ?>

            <h2 class="h6 border-bottom pb-1 mb-3">Dados pessoais</h2>
            <?= view('pessoas/_form_pessoa', [
                'pessoa'   => null,
                'telefone' => null,
                'cidades'  => $cidades,
                'clientes' => $clientes,
            ]) ?>

            <h2 class="h6 border-bottom pb-1 mb-3">Solicitação (opcional)</h2>
            <p class="text-muted small">Preencha somente se desejar registrar uma solicitação junto ao cadastro.</p>
            <?= view('solicitacoes/_form_solicitacao', [
                'solicitacao'   => null,
                'agentes'       => $agentes,
                'procedimentos' => $procedimentos,
            ]) ?>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="<?= site_url('pessoas') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
