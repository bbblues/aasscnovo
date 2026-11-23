<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<h1 class="h4 mb-3 titulo_dashboard"><?= esc($title) ?></h1>

<?= view('_filtros', [
    'action'    => $apenasAbertas ? 'solicitacoes/abertas' : 'solicitacoes',
    'limparUrl' => 'solicitacoes/limpar',
    'filtros'   => $filtros,
    'clientes'  => $clientes,
]) ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped table-sm align-middle tabela-dashboard mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pessoa</th>
                    <th>Pedido</th>
                    <th>Procedimento</th>
                    <th>Responsável</th>
                    <th>Cidade</th>
                    <th>Cliente</th>
                    <th>Situação</th>
                    <th class="text-center">Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($solicitacoes)): ?>
                <tr><td colspan="9" class="text-center text-muted py-3">Nenhuma solicitação encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($solicitacoes as $s): ?>
                    <tr>
                        <td class="nao-quebrar-linha"><?= esc(! empty($s['criado']) ? date('d/m/y', strtotime($s['criado'])) : '—') ?></td>
                        <td><?= esc($s['pes_nome']) ?></td>
                        <td><?= esc($s['pedido']) ?></td>
                        <td><?= esc($s['procedimento'] ?? '') ?></td>
                        <td><?= esc($s['responsavel']) ?></td>
                        <td><?= esc($s['pes_cidade'] ?? '') ?></td>
                        <td><?= esc($s['cliente'] ?? '') ?></td>
                        <td><?= esc(situacao_by_id($s['sol_sit_id'])) ?></td>
                        <td class="text-center">
                            <a href="<?= site_url('solicitacoes/edit/' . $s['pes_id']) ?>" title="Abrir">
                                <img src="<?= base_url('assets/img/procurar.svg') ?>" alt="Abrir" width="18" height="18">
                            </a>
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
