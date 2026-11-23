<?php
/**
 * Campos do formulário de pessoa (reutilizado em editar/cadastrar).
 *
 * Espera: $pessoa (array|null), $telefone (string|null), $cidades, $clientes.
 */
?>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="pes_nome">Nome <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="pes_nome" name="pes_nome" maxlength="50" required value="<?= esc(set_value('pes_nome', $pessoa['pes_nome'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="pes_cidade">Cidade</label>
        <select class="form-select" id="pes_cidade" name="pes_cidade">
            <option value="">Selecione...</option>
            <?php foreach ($cidades as $c): ?>
                <option value="<?= esc($c['cinome']) ?>" <?= set_select('pes_cidade', $c['cinome'], ($pessoa['pes_cidade'] ?? '') === $c['cinome']) ?>><?= esc($c['cinome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="field_telefones_value">Telefone</label>
        <input type="text" class="form-control" id="field_telefones_value" name="field_telefones_value" maxlength="253" value="<?= esc(set_value('field_telefones_value', $telefone ?? '')) ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="endereco">Endereço</label>
        <input type="text" class="form-control" id="endereco" name="endereco" maxlength="92" value="<?= esc(set_value('endereco', $pessoa['endereco'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="cep">CEP</label>
        <input type="text" class="form-control" id="cep" name="cep" maxlength="9" value="<?= esc(set_value('cep', $pessoa['cep'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="nascimento">Nascimento</label>
        <input type="text" class="form-control" id="nascimento" name="nascimento" maxlength="34" placeholder="ex.: 01/01/1990" value="<?= esc(set_value('nascimento', $pessoa['nascimento'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="rg">RG</label>
        <input type="text" class="form-control" id="rg" name="rg" maxlength="14" value="<?= esc(set_value('rg', $pessoa['rg'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="cpf">CPF</label>
        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="16" value="<?= esc(set_value('cpf', $pessoa['cpf'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="sus">SUS</label>
        <input type="text" class="form-control" id="sus" name="sus" maxlength="20" value="<?= esc(set_value('sus', $pessoa['sus'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="mae">Nome da mãe</label>
        <input type="text" class="form-control" id="mae" name="mae" maxlength="45" value="<?= esc(set_value('mae', $pessoa['mae'] ?? '')) ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="email">E-mail <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email" name="email" maxlength="100" required value="<?= esc(set_value('email', $pessoa['email'] ?? '')) ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="sexo">Sexo</label>
        <select class="form-select" id="sexo" name="sexo">
            <option value="">Selecione...</option>
            <?php foreach (['Masculino', 'Feminino', 'outro'] as $sexo): ?>
                <option value="<?= esc($sexo) ?>" <?= set_select('sexo', $sexo, ($pessoa['sexo'] ?? '') === $sexo) ?>><?= esc($sexo) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label" for="pes_cliente">Cliente</label>
        <select class="form-select" id="pes_cliente" name="pes_cliente">
            <option value="">Selecione...</option>
            <?php foreach ($clientes as $c): ?>
                <?php
                    $labelCliente = trim(($c['username'] ?? '') . ' ' . ($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''));
                    $labelCliente = $labelCliente !== '' ? $labelCliente : ($c['email'] ?? $c['id']);
                ?>
                <option value="<?= esc($c['id']) ?>" <?= set_select('pes_cliente', (string) $c['id'], (string) ($pessoa['pes_cliente'] ?? '') === (string) $c['id']) ?>><?= esc($labelCliente) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
