<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Trocar senha</div>
            <div class="card-body">
                <?= form_open('auth/change_password') ?>
                    <div class="mb-3">
                        <label for="old" class="form-label">Senha atual</label>
                        <input type="password" class="form-control" id="old" name="old" required>
                    </div>
                    <div class="mb-3">
                        <label for="new" class="form-label">Nova senha</label>
                        <input type="password" class="form-control" id="new" name="new" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_confirm" class="form-label">Confirmar nova senha</label>
                        <input type="password" class="form-control" id="new_confirm" name="new_confirm" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="<?= site_url('/') ?>" class="btn btn-secondary">Cancelar</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
