<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3 titulo_dashboard"><?= esc($titulo) ?></h1>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= esc($titulo) ?></span>
        <a href="<?= site_url($rotaBase . '/add') ?>" class="btn btn-sm btn-primary">Novo</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($registros)): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">Nenhum registro encontrado.</td></tr>
            <?php else: ?>
                <?php foreach ($registros as $r): ?>
                    <tr>
                        <td><?= esc($r[$id]) ?></td>
                        <td><?= esc($r[$coluna]) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url($rotaBase . '/edit/' . $r[$id]) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <a href="<?= site_url($rotaBase . '/remove/' . $r[$id]) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover este registro?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
