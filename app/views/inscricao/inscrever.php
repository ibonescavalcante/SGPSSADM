<?php $this->layout('template') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Confirmar Inscrição</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Dados da Inscrição</h5>
                        <p><strong>PSS:</strong> <?= htmlspecialchars($pss['titulo']) ?></p>
                        <p><strong>Cargo:</strong> <?= htmlspecialchars($cargo['nome']) ?></p>
                        <p><strong>Candidato:</strong> <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></p>
                    </div>

                    <form method="POST" action="/inscricao/confirmar">
                        <input type="hidden" name="pss_id" value="<?= $pss['id'] ?>">
                        <input type="hidden" name="cargo_id" value="<?= $cargo['id'] ?>">
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="aceito_termos" name="aceito_termos" required>
                                <label class="form-check-label" for="aceito_termos">
                                    Declaro que li e aceito os termos do edital e que as informações prestadas são verdadeiras.
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Confirmar Inscrição</button>
                            <a href="/" class="btn btn-outline-secondary btn-lg">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

