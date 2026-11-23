<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3 titulo_dashboard">Pessoas</h1>

<?= view('_filtros', [
    'action'    => 'pessoas',
    'limparUrl' => 'pessoas/limpar',
    'filtros'   => $filtros,
    'clientes'  => $clientes,
]) ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle tabela-dashboard mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Nome</th>
                    <th>Cidade</th>
                    <th>Cliente</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th class="text-center">Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pessoas)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Nenhuma pessoa encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($pessoas as $p): ?>
                    <tr>
                        <td class="nao-quebrar-linha"><?= esc(! empty($p['pes_pub']) ? date('d/m/y', strtotime($p['pes_pub'])) : '—') ?></td>
                        <td><?= esc($p['pes_nome']) ?></td>
                        <td><?= esc($p['pes_cidade'] ?? '') ?></td>
                        <td><?= esc($p['cliente'] ?? '') ?></td>
                        <td><?= esc($p['email']) ?></td>
                        <td><?= esc($p['telefone'] ?? '') ?></td>
                        <td class="text-center">
                            <a href="<?= site_url('pessoas/edit/' . $p['pes_id']) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $links ?>

<?= $this->endSection() ?>
