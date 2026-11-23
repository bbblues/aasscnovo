<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3 titulo_dashboard">Relatório de Atividades</h1>

<?= view('_filtros', [
    'action'    => 'relatorios',
    'limparUrl' => 'relatorios/limpar',
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
                    <th>Pedido</th>
                    <th>Atividade</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($atividades)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Nenhuma atividade encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($atividades as $a): ?>
                    <tr>
                        <td class="nao-quebrar-linha"><?= esc(! empty($a['created']) ? date('d/m/Y', (int) $a['created']) : '—') ?></td>
                        <td><?= esc($a['nome'] ?? '') ?></td>
                        <td><?= esc($a['pedido'] ?? '') ?></td>
                        <td><?= esc($a['atividade'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
