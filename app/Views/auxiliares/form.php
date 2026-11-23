<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3"><?= $registro ? 'Editar' : 'Novo' ?> <?= esc($tituloSingular) ?></h1>

<div class="card">
    <div class="card-body">
        <?= form_open($action) ?>
            <div class="mb-3">
                <label for="<?= esc($coluna) ?>" class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="<?= esc($coluna) ?>" name="<?= esc($coluna) ?>" maxlength="255" required value="<?= esc(set_value($coluna, $registro[$coluna] ?? '')) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= site_url($rotaBase) ?>" class="btn btn-secondary">Cancelar</a>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
