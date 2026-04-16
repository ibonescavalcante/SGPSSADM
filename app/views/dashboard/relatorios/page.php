<?php $this->layout("dashboard/template") ?>
<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Gerenciamento de Relatórios</h2>
    </div>
    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?php
            echo htmlspecialchars($_SESSION['erro']);
            // apaga logo em seguida
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>
    <!-- Filtros -->
    <!--div class="card">
        <div class="card-header">
            <h5 class="mb-0">Filtros de Relatórios</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="processoFilter" class="form-label">Processo Seletivo</label>
                    <select class="form-select" id="processoFilter" name="processo_id">
                        <option value="">Selecione o processo</option>
                        <?php foreach ($processos as $processo): ?>
                            <option value="<?php echo $processo['id']; ?>">
                                <?php echo htmlspecialchars($processo['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="statusRelatorio" class="form-label">Status</label>
                    <select class="form-select" id="statusRelatorio">
                        <option value="0">Todos</option>
                        <option value="1">Deferido</option>
                        <option value="2">Indeferido</option>

                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="tipoRelatorio" class="form-label">Tipo</label>
                    <select class="form-select" id="tipoRelatorio">
                        <option value="0">Todos</option>
                        <option value="1">PDC</option>
                        <option value="2">Negros</option>
                        <option value="3">Indigina</option>

                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="cargoRelatorio" class="form-label">Cargo</label>
                    <select class="form-select" id="cargoRelatorio">
                        <option value="0">Todos</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>

                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="formatoFilter" class="form-label">Formato</label>
                    <select class="form-select" id="formatoFilter">
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100  mb-3" id="gerarRelatorio">
                        <i class="fas fa-file-alt me-2"></i>Gerar Relatório
                    </button>
                </div>
            </div>
        </div>
    </div-->

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Relatórios predefinidos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="process_filter_relatorio_predefinido" class="form-label">Processo Seletivo</label>
                    <select class="form-select" id="process_filter_relatorio_predefinido"
                        name="process_filter_relatorio_predefinido">
                        <option value="">Selecione o processo</option>
                        <?php foreach ($processos as $processo): ?>
                            <option value="<?php echo $processo['id']; ?>">
                                <?php echo htmlspecialchars($processo['titulo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tipoRelatorioPredefinido" class="form-label">Modelo de Relatório</label>
                    <select class="form-select" id="tipoRelatorioPredefinido">
                        <option value="1">Situação de Inscrição por Vaga</option>
                        <option value="5">Resultado Preliminar Deferidas PcD</option>
                        <option value="7">Resultado Preliminar Deferidas Ampla</option>
                        <option value="6">Resultado Preliminar Análise Curricular</option>
                        <option value="2">Resultado Definitivo Classificatório Final</option>
                        <option value="3">Resultado Definitivo Deferidas PcD</option>
                        <option value="4">Resultado Definitivo Análise Curricular</option>
                        <option value="8">Homologação</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="formatoFilter" class="form-label">Formato</label>
                    <select class="form-select" id="formatoFilter">
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100  mb-3" id="gerarRelatorio_predefinido">
                        <i class="fas fa-file-alt me-2"></i>Gerar Relatório
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('gerarRelatorio_predefinido').addEventListener('click', function() {
            const processoId = document.getElementById('process_filter_relatorio_predefinido').value;
            const tipoRelatorio = document.getElementById('tipoRelatorioPredefinido').value;
            const formato = document.getElementById('formatoFilter').value;

            if (!processoId) {
                alert('Por favor, selecione um Processo Seletivo.');
                return;
            }

            if (!formato) {
                alert('Por favor, selecione um formato para o relatório.');
                return;
            }

            // Constrói a URL com os parâmetros
            const params = new URLSearchParams({
                processo_id: processoId,
                tipo_relatorio: tipoRelatorio,
                formato: formato
                // Adicione outros filtros aqui se necessário, como 'cargo_id', 'status', etc.
            });
            const url = `/relatorios/gerar?${params.toString()}`;
            console.log(url);

            // Redireciona para a URL de geração, que iniciará o download
            window.location.href = url;
        });
    });
</script>