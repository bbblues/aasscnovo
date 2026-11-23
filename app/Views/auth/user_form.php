<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3"><?= $user ? 'Editar usuário' : 'Novo usuário' ?></h1>

<div class="card">
    <div class="card-body">
        <?= form_open($user ? 'auth/edit_user/' . $user['id'] : 'auth/create_user') ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" maxlength="50" value="<?= set_value('first_name', $user['first_name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Sobrenome</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" maxlength="50" value="<?= set_value('last_name', $user['last_name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Nome de usuário</label>
                    <input type="text" class="form-control" id="username" name="username" maxlength="100" value="<?= set_value('username', $user['username'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" maxlength="100" required value="<?= set_value('email', $user['email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="company" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="company" name="company" maxlength="100" value="<?= set_value('company', $user['company'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="phone" name="phone" maxlength="20" value="<?= set_value('phone', $user['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Senha <?= $user ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" <?= $user ? '' : 'required' ?> placeholder="<?= $user ? 'Deixe em branco para manter' : '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirm" class="form-label">Confirmar senha <?= $user ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" <?= $user ? '' : 'required' ?>>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label d-block">Grupos</label>
                    <?php foreach ($grupos as $g): ?>
                        <?php $marcado = in_array($g['id'], $gruposSelecionados ?? [], true); ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="groups[]" id="g<?= esc($g['id']) ?>" value="<?= esc($g['id']) ?>" <?= set_checkbox('groups[]', (string) $g['id'], $marcado) ?>>
                            <label class="form-check-label" for="g<?= esc($g['id']) ?>"><?= esc($g['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= site_url('auth') ?>" class="btn btn-secondary">Cancelar</a>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
