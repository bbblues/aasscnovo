<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
$labelsPercentual  = array_keys($graficoPercentual);
$valoresPercentual = array_values($graficoPercentual);
$labelsAbsoluto    = array_keys($graficoAbsoluto);
$valoresAbsoluto   = array_values($graficoAbsoluto);
$labelsSituacao    = array_keys($porSituacao);
$valoresSituacao   = array_values($porSituacao);
?>

<h1 class="h4 mb-3 titulo_dashboard">Suas solicitações em aberto</h1>

<div class="card mb-4">
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
            <?php if (empty($solicitacoesAbertas)): ?>
                <tr><td colspan="9" class="text-center text-muted py-3">Nenhuma solicitação em aberto.</td></tr>
            <?php else: ?>
                <?php foreach ($solicitacoesAbertas as $s): ?>
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

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header">% das solicitações abertas no último ano</div>
            <div class="card-body">
                <div style="height: 280px;"><canvas id="graficoPercentual"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header">Número de solicitações abertas no último ano</div>
            <div class="card-body">
                <div style="height: 280px;"><canvas id="graficoAbsoluto"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header">Solicitações por situação nos últimos 30 dias</div>
            <div class="card-body">
                <div style="height: 280px;"><canvas id="graficoSituacao"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const corPrimaria = '#17a2b8';
    const paleta = ['#17a2b8', '#117a8b', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'];

    new Chart(document.getElementById('graficoPercentual'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsPercentual, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: '%',
                data: <?= json_encode($valoresPercentual) ?>,
                backgroundColor: corPrimaria
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('graficoAbsoluto'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsAbsoluto, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Solicitações',
                data: <?= json_encode($valoresAbsoluto) ?>,
                backgroundColor: corPrimaria
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('graficoSituacao'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labelsSituacao, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                data: <?= json_encode($valoresSituacao) ?>,
                backgroundColor: paleta
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
})();
</script>
<?= $this->endSection() ?>
