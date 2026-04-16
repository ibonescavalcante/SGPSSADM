<?php $this->layout("dashboard/template"); ?>
<?php
// echo ("<pre>");
// var_dump($detalhes);
// // var_dump($pontuacao);
// echo ("<pre>");
// die;
?>

<!-- Conteúdo -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <div class="card card-custom">
        <div class="card-body">
            <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['erro']); ?>
                <?php unset($_SESSION['erro']); ?>
            </div>
            <?php endif; ?>

            <!-- Dados do candidato -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Processo:</strong> <?= htmlspecialchars($detalhes[0]->processo_nome); ?></p>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($detalhes[0]->nome); ?></p>
                    <p><strong>CPF:</strong> <?= htmlspecialchars($detalhes[0]->cpf); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Zona:</strong> <?= htmlspecialchars(ucfirst($detalhes[0]->zona)); ?></p>
                    <p><strong>Cargo:</strong> <?= htmlspecialchars($detalhes[0]->nome_do_cargo); ?></p>
                    <p><strong>Inscrição:</strong> <a
                            href="/dashboard/inscricoes/detalhes/<?php echo $detalhes[0]->inscricao_id; ?>"
                            target="_blank"
                            rel="noopener noreferrer"><?= htmlspecialchars($detalhes[0]->inscricao); ?></a>
                    </p>
                    <p><strong>Status:</strong>
                        <span
                            class="badge 
                            <?= ($detalhes[0]->status == 'deferido') ? 'bg-success' : (($detalhes[0]->status == 'indeferido') ? 'bg-danger' : 'bg-warning'); ?>">
                            <?= htmlspecialchars(ucfirst($detalhes[0]->status)); ?>
                        </span>
                    </p>
                    <p><strong>Avaliador:</strong> <?= htmlspecialchars($detalhes[0]->avaliador ?? ''); ?></p>
                </div>
            </div>

            <!-- Descrição -->
            <?php if (!empty($detalhes[0]->descricao)): ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-2">Descrição</h5>
                    <div class="alert alert-warning shadow-sm p-3 rounded" style="border-left: 5px solid #d39e00;">
                        <p class="mb-0 text-dark fw-semibold">
                            <?= htmlspecialchars($detalhes[0]->descricao); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recurso -->
            <?php if (!empty($detalhes[0]->recurso)): ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-2">📋 Recurso</h5>
                    <p><?= nl2br(htmlspecialchars($detalhes[0]->recurso)); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulário de Justificativa e Status -->
            <form action="" method="post" class="p-3 bg-light rounded border">
                <?php if (!empty($_SESSION['csrf_token'])): ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="justificativa" class="form-label fw-bold">Justificativa</label>
                    <textarea name="justificativa" id="justificativa" rows="3" class="form-control"
                        placeholder="Escreva sua resposta aqui..."><?=
                                                                    htmlspecialchars($detalhes[0]->justificativa ?? ''); ?></textarea>
                </div>
                <input type="hidden" name="avaliador_id"
                    value="<?= htmlspecialchars($detalhes[0]->avaliador ?? ''); ?>" />
                <input type="hidden" name="recurso_id" value="<?= htmlspecialchars($detalhes[0]->id); ?>" />

                <div class="d-flex justify-content-between align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='/dashboard/recursos'">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </button>

                    <div class="d-flex gap-2">
                        <select name="status" id="status" class="form-select w-auto">
                            <option value="DEFERIDO" <?= ($detalhes[0]->status == 'deferido') ? 'selected' : ''; ?>>
                                Deferida</option>
                            <option value="INDEFERIDO" <?= ($detalhes[0]->status == 'indeferido') ? 'selected' : ''; ?>>
                                Indeferida</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>