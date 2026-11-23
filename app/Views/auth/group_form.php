<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $group ? 'Editar grupo' : 'Novo grupo' ?></h1>

<div class="card">
    <div class="card-body">
        <?= form_open($group ? 'auth/edit_group/' . $group['id'] : 'auth/create_group') ?>
            <div class="mb-3">
                <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" maxlength="20" required value="<?= set_value('name', $group['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <input type="text" class="form-control" id="description" name="description" maxlength="100" value="<?= set_value('description', $group['description'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= site_url('auth') ?>" class="btn btn-secondary">Cancelar</a>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
