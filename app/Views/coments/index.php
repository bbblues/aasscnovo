<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3 titulo_dashboard">Comentários</h1>

<?= view('_filtros', [
    'action'    => 'coments',
    'limparUrl' => 'coments/limpar',
    'filtros'   => $filtros,
    'clientes'  => $clientes,
]) ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle tabela-dashboard mb-0">
            <thead>
                <tr>
                    <th></th>
                    <th>Data</th>
                    <th>Pessoa</th>
                    <th>Pedido</th>
                    <th>Atualização</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($comentarios)): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">Nenhum comentário encontrado.</td></tr>
            <?php else: ?>
                <?php foreach ($comentarios as $c): ?>
                    <tr>
                        <td>
                            <img class="avatar" src="<?= base_url('assets/img/avatares/' . simple_lower((string) ($c['name'] ?? '')) . '.jpg') ?>" alt="" onerror="this.style.display='none'">
                        </td>
                        <td class="nao-quebrar-linha"><?= esc(! empty($c['changed']) ? date('d/m/y', (int) $c['changed']) : '—') ?></td>
                        <td><?= esc($c['pes_nome']) ?></td>
                        <td><?= esc($c['pedido']) ?></td>
                        <td><?= esc($c['subject']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $links ?>

<?= $this->endSection() ?>
