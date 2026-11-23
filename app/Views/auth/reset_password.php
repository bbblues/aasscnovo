<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="card card-auth shadow-sm">
    <div class="card-body">
        <h4 class="text-center mb-4">Definir nova senha</h4>
        <?= form_open('auth/reset_password/' . esc($code, 'url')) ?>
            <div class="mb-3">
                <label for="password" class="form-label">Nova senha</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirme a nova senha</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Redefinir senha</button>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
