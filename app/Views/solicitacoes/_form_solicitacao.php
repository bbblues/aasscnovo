<?php
/**
 * Campos do formulário de solicitação (reutilizado no accordion e no
 * cadastro de pessoa com solicitação opcional).
 *
 * Espera: $solicitacao (array|null), $agentes, $procedimentos.
 */
$sitDefault = (int) ($solicitacao['sol_sit_id'] ?? 1);
?>
<div class="row">
    <div class="col-md-6 mb-2">
        <label class="form-label" for="sagente">Agente</label>
        <select class="form-select form-select-sm" id="sagente" name="sagente">
            <option value="">Selecione...</option>
            <?php foreach ($agentes as $a): ?>
                <?php
                    $labelAgente = trim(($a['username'] ?? '') . ' ' . ($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
                    $labelAgente = $labelAgente !== '' ? $labelAgente : ($a['email'] ?? $a['id']);
                ?>
                <option value="<?= esc($a['id']) ?>" <?= set_select('sagente', (string) $a['id'], (int) ($solicitacao['sagente'] ?? 0) === (int) $a['id']) ?>><?= esc($labelAgente) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label class="form-label" for="sol_sit_id">Situação</label>
        <select class="form-select form-select-sm" id="sol_sit_id" name="sol_sit_id">
            <option value="1" <?= set_select('sol_sit_id', '1', $sitDefault === 1) ?>>Aguardando Resp</option>
            <option value="2" <?= set_select('sol_sit_id', '2', $sitDefault === 2) ?>>Em Andamento</option>
            <option value="3" <?= set_select('sol_sit_id', '3', $sitDefault === 3) ?>>Finalizado</option>
        </select>
    </div>
    <div class="col-12 mb-2">
        <label class="form-label" for="pedido">Pedido <span class="text-danger">*</span></label>
        <textarea class="form-control" id="pedido" name="pedido" rows="2" required><?= esc(set_value('pedido', $solicitacao['pedido'] ?? '')) ?></textarea>
    </div>
    <div class="col-md-6 mb-2">
        <label class="form-label" for="procedimento">Procedimento</label>
        <select class="form-select form-select-sm" id="procedimento" name="procedimento">
            <option value="">Selecione...</option>
            <?php foreach ($procedimentos as $p): ?>
                <option value="<?= esc($p['med_nome']) ?>" <?= set_select('procedimento', $p['med_nome'], ($solicitacao['procedimento'] ?? '') === $p['med_nome']) ?>><?= esc($p['med_nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label class="form-label" for="responsavel">Responsável <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" id="responsavel" name="responsavel" maxlength="255" required value="<?= esc(set_value('responsavel', $solicitacao['responsavel'] ?? '')) ?>">
    </div>
    <div class="col-12 mb-2">
        <label class="form-label" for="obs">Observação</label>
        <textarea class="form-control" id="obs" name="obs" rows="2"><?= esc(set_value('obs', $solicitacao['obs'] ?? '')) ?></textarea>
    </div>
</div>
