<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="card card-auth shadow-sm">
    <div class="card-body">
        <h4 class="text-center mb-4">Recuperar senha</h4>
        <p class="text-muted">Informe seu e-mail para receber as instruções de redefinição.</p>
        <?= form_open('auth/forgot_password') ?>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar</button>
        <?= form_close() ?>
        <div class="text-center mt-3">
            <a href="<?= site_url('auth/login') ?>">Voltar ao login</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
