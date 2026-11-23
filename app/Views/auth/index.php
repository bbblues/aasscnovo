<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Gestão de usuários e grupos</h1>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Usuários</span>
        <a href="<?= site_url('auth/create_user') ?>" class="btn btn-sm btn-primary">Novo usuário</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Grupos</th>
                    <th>Status</th>
                    <th>Último login</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Nenhum usuário cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <?php
                        $nomeCompleto = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                        $ativo = ($u['active'] === null || (int) $u['active'] === 1);
                        $nomesGrupos = array_column($u['grupos'] ?? [], 'name');
                    ?>
                    <tr>
                        <td><?= esc($u['id']) ?></td>
                        <td><?= esc($nomeCompleto !== '' ? $nomeCompleto : ($u['username'] ?? '—')) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td><?= $nomesGrupos ? esc(implode(', ', $nomesGrupos)) : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <?php if ($ativo): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u['last_login'] ? esc(date('d/m/Y H:i', (int) $u['last_login'])) : '—' ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('auth/edit_user/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <?php if ((int) $u['id'] !== (int) session()->get('user_id')): ?>
                                <?php if ($ativo): ?>
                                    <?= form_open('auth/deactivate/' . $u['id'], ['style' => 'display:inline']) ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Desativar este usuário?')">Desativar</button>
                                    <?= form_close() ?>
                                <?php else: ?>
                                    <?= form_open('auth/activate/' . $u['id'], ['style' => 'display:inline']) ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success">Ativar</button>
                                    <?= form_close() ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Grupos</span>
        <a href="<?= site_url('auth/create_group') ?>" class="btn btn-sm btn-primary">Novo grupo</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr><th>ID</th><th>Nome</th><th>Descrição</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($grupos)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Nenhum grupo cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($grupos as $g): ?>
                    <tr>
                        <td><?= esc($g['id']) ?></td>
                        <td><?= esc($g['name']) ?></td>
                        <td><?= esc($g['description']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('auth/edit_group/' . $g['id']) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
