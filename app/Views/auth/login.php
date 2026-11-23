<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="card card-auth shadow-sm">
    <div class="card-body">
        <h4 class="text-center mb-4">Sistema AASSC</h4>
        <?= form_open('auth/login') ?>
            <div class="mb-3">
                <label for="identity" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="identity" name="identity" value="<?= set_value('identity') ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <?= form_close() ?>
        <div class="text-center mt-3">
            <a href="<?= site_url('auth/forgot_password') ?>">Esqueci minha senha</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
