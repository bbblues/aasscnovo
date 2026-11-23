<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="row justify-content-around">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Atualizar cadastro da pessoa</div>
            <div class="card-body">
                <?= form_open('pessoas/edit/' . $pessoa['pes_id'], ['onsubmit' => "return confirm('Atualizar cadastro?')"]) ?>
                    <?= view('pessoas/_form_pessoa', [
                        'pessoa'   => $pessoa,
                        'telefone' => $telefone,
                        'cidades'  => $cidades,
                        'clientes' => $clientes,
                    ]) ?>
                    <button type="submit" class="btn btn-primary btn-sm">Atualizar cadastro</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <h2 class="h5 mb-3"><?= esc($pessoa['pes_nome']) ?></h2>

        <?php if (empty($solicitacoes)): ?>
            <div class="alert alert-light border">Nenhuma solicitação cadastrada para esta pessoa.</div>
        <?php endif; ?>

        <div class="accordion" id="accordionSolicitacoes">
            <?php foreach ($solicitacoes as $i => $s): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="h<?= (int) $s['sid'] ?>">
                        <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#sol<?= (int) $s['sid'] ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="sol<?= (int) $s['sid'] ?>">
                            <?= esc($s['pedido']) ?> — <?= esc(! empty($s['criado']) ? date('d/m/y', strtotime($s['criado'])) : '') ?>
                        </button>
                    </h2>
                    <div id="sol<?= (int) $s['sid'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" aria-labelledby="h<?= (int) $s['sid'] ?>" data-bs-parent="#accordionSolicitacoes">
                        <div class="accordion-body">
                            <?= form_open('solicitacoes/update/' . $s['sid'], ['onsubmit' => "return confirm('Atualizar solicitação?')"]) ?>
                                <?= view('solicitacoes/_form_solicitacao', [
                                    'solicitacao'   => $s,
                                    'agentes'       => $agentes,
                                    'procedimentos' => $procedimentos,
                                ]) ?>
                                <button type="submit" class="btn btn-primary btn-sm">Salvar solicitação</button>
                            <?= form_close() ?>

                            <hr>

                            <h6 class="mb-2">Comentários / Atualizações</h6>

                            <?php if (empty($s['comentarios'])): ?>
                                <p class="text-muted small mb-2">Nenhum comentário.</p>
                            <?php else: ?>
                                <div class="list-group mb-3">
                                    <?php foreach ($s['comentarios'] as $com): ?>
                                        <?php $podeEditar = (int) ($com['uid'] ?? 0) === (int) session()->get('user_id'); ?>
                                        <div class="list-group-item py-2">
                                            <div class="small text-muted mb-1">
                                                <?= esc($com['name'] ?? '—') ?> — <?= esc(date('d/m/y H:i', (int) $com['created'])) ?>
                                            </div>
                                            <?php if ($podeEditar): ?>
                                                <?= form_open('coments/update/' . $com['cid'], ['class' => 'm-0']) ?>
                                                    <textarea class="form-control form-control-sm mb-1" name="subject" rows="2" required><?= esc($com['subject']) ?></textarea>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Salvar</button>
                                                <?= form_close() ?>
                                            <?php else: ?>
                                                <textarea class="form-control form-control-sm" rows="2" disabled><?= esc($com['subject']) ?></textarea>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?= form_open('coments/add/' . $pessoa['pes_id'], ['class' => 'm-0']) ?>
                                <input type="hidden" name="nid" value="<?= (int) $s['sid'] ?>">
                                <label class="form-label small mb-1">Novo comentário</label>
                                <textarea class="form-control form-control-sm mb-1" name="subject" rows="2" required placeholder="Escreva uma atualização..."></textarea>
                                <button type="submit" class="btn btn-sm btn-primary">Adicionar comentário</button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="accordion-item">
                <h2 class="accordion-header" id="hNova">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#novaSolicitacao" aria-expanded="false" aria-controls="novaSolicitacao">
                        Cadastrar Nova Solicitação
                    </button>
                </h2>
                <div id="novaSolicitacao" class="accordion-collapse collapse" aria-labelledby="hNova" data-bs-parent="#accordionSolicitacoes">
                    <div class="accordion-body">
                        <?= form_open('solicitacoes/add') ?>
                            <input type="hidden" name="spessoa" value="<?= (int) $pessoa['pes_id'] ?>">
                            <?= view('solicitacoes/_form_solicitacao', [
                                'solicitacao'   => null,
                                'agentes'       => $agentes,
                                'procedimentos' => $procedimentos,
                            ]) ?>
                            <button type="submit" class="btn btn-primary btn-sm">Cadastrar solicitação</button>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
