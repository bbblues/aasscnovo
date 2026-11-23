<?php
/**
 * Formulário de filtros compartilhado pelas listagens (pessoas/solicitações).
 *
 * Espera: $action, $limparUrl, $filtros, $clientes.
 */
?>
<div class="card form_topo">
    <div class="card-body">
        <?= form_open($action, ['class' => 'row g-2 align-items-end']) ?>
            <div class="col-md-2">
                <label class="form-label" for="f_responsavel">Responsável</label>
                <input type="text" class="form-control form-control-sm" id="f_responsavel" name="responsavel" value="<?= esc($filtros['responsavel'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_cliente">Cliente</label>
                <select class="form-select form-select-sm" id="f_cliente" name="cliente">
                    <option value="">Todos</option>
                    <?php foreach ($clientes as $c): ?>
                        <?php
                            $labelCliente = trim(($c['username'] ?? '') . ' ' . ($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''));
                            $labelCliente = $labelCliente !== '' ? $labelCliente : ($c['email'] ?? $c['id']);
                        ?>
                        <option value="<?= esc($c['id']) ?>" <?= set_select('cliente', (string) $c['id'], (string) ($filtros['cliente'] ?? '') === (string) $c['id']) ?>><?= esc($labelCliente) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_pessoa">Pessoa</label>
                <input type="text" class="form-control form-control-sm" id="f_pessoa" name="pessoa" value="<?= esc($filtros['pessoa'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_municipio">Município</label>
                <input type="text" class="form-control form-control-sm" id="f_municipio" name="municipio" value="<?= esc($filtros['municipio'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_data_inicio">Data início</label>
                <input type="date" class="form-control form-control-sm" id="f_data_inicio" name="data_inicio" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="f_data_fim">Data fim</label>
                <input type="date" class="form-control form-control-sm" id="f_data_fim" name="data_fim" value="<?= esc($filtros['data_fim'] ?? '') ?>">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                <a href="<?= site_url($limparUrl) ?>" class="btn btn-sm btn-secondary">Limpar</a>
            </div>
        <?= form_close() ?>
    </div>
</div>
