<?php $this->layout('admin_template', ['title' => 'Criar Novo PSS']) ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">
                        <i class="fas fa-plus-circle text-success"></i>
                        Criar Novo PSS
                    </h1>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                        <button type="button" class="btn btn-success" id="salvarPss">
                            <i class="fas fa-save"></i> Salvar PSS
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Navegação das Abas -->
                    <ul class="nav nav-tabs mb-4" id="pssTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basicas-tab" data-bs-toggle="tab" data-bs-target="#basicas" type="button" role="tab">
                                <i class="fas fa-info-circle"></i> Informações Básicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="etapas-tab" data-bs-toggle="tab" data-bs-target="#etapas" type="button" role="tab">
                                <i class="fas fa-calendar-alt"></i> Etapas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cargos-tab" data-bs-toggle="tab" data-bs-target="#cargos" type="button" role="tab">
                                <i class="fas fa-briefcase"></i> Cargos/Vagas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="criterios-tab" data-bs-toggle="tab" data-bs-target="#criterios" type="button" role="tab">
                                <i class="fas fa-trophy"></i> Critérios de Desempate
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button" role="tab">
                                <i class="fas fa-cog"></i> Configurações
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab">
                                <i class="fas fa-history"></i> Auditoria
                            </button>
                        </li>
                    </ul>

                    <!-- Formulário -->
                    <form id="formPss" method="POST" action="/admin/pss/salvar">
                        <!-- Conteúdo das Abas -->
                        <div class="tab-content" id="pssTabContent">
                            
                            <!-- Aba 1: Informações Básicas -->
                            <div class="tab-pane fade show active" id="basicas" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="titulo" class="form-label">Título do PSS *</label>
                                            <input type="text" class="form-control" id="titulo" name="titulo" required 
                                                   placeholder="Ex: PSS Prefeitura Municipal - Elementar e Auxiliar">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="ano_exercicio" class="form-label">Ano de Exercício *</label>
                                            <input type="number" class="form-control" id="ano_exercicio" name="ano_exercicio" 
                                                   min="2024" max="2030" value="2025" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="secretaria" class="form-label">Secretaria Responsável</label>
                                    <input type="text" class="form-control" id="secretaria" name="secretaria" 
                                           placeholder="Ex: Secretaria Municipal de Administração">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="4" 
                                              placeholder="Descrição detalhada do processo seletivo..."></textarea>
                                </div>
                            </div>

                            <!-- Aba 2: Etapas -->
                            <div class="tab-pane fade" id="etapas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Etapas do Processo Seletivo</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="adicionarEtapa">
                                        <i class="fas fa-plus"></i> Adicionar Etapa
                                    </button>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Importante:</strong> A etapa de inscrição é obrigatória e sempre será a primeira.
                                </div>
                                
                                <div id="etapasContainer">
                                    <!-- Etapa de Inscrição (obrigatória) -->
                                    <div class="card mb-3 etapa-card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-plus"></i> Etapa 1: Inscrição (Obrigatória)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Data/Hora de Início *</label>
                                                    <input type="datetime-local" class="form-control" name="etapas[0][dt_ini]" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Data/Hora de Fim *</label>
                                                    <input type="datetime-local" class="form-control" name="etapas[0][dt_fim]" required>
                                                </div>
                                            </div>
                                            <input type="hidden" name="etapas[0][nome]" value="Inscrição">
                                            <input type="hidden" name="etapas[0][descricao]" value="Período de inscrições do processo seletivo">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba 3: Cargos/Vagas -->
                            <div class="tab-pane fade" id="cargos" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Cargos e Vagas</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="adicionarCargo">
                                        <i class="fas fa-plus"></i> Adicionar Cargo
                                    </button>
                                </div>
                                
                                <div id="cargosContainer">
                                    <!-- Cargos serão adicionados dinamicamente -->
                                </div>
                            </div>

                            <!-- Aba 4: Critérios de Desempate -->
                            <div class="tab-pane fade" id="criterios" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Critérios de Desempate</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="adicionarCriterio">
                                        <i class="fas fa-plus"></i> Adicionar Critério
                                    </button>
                                </div>
                                
                                <div id="criteriosContainer">
                                    <!-- Critérios padrão -->
                                    <div class="card mb-2 criterio-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">1º</span>
                                                <input type="text" class="form-control me-2" name="criterios[0][criterio]" 
                                                       value="Idoso com 60 anos ou mais (prioridade pelo Estatuto do Idoso)">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerCriterio(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-2 criterio-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">2º</span>
                                                <input type="text" class="form-control me-2" name="criterios[1][criterio]" 
                                                       value="Maior pontuação em experiência profissional">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerCriterio(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-2 criterio-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">3º</span>
                                                <input type="text" class="form-control me-2" name="criterios[2][criterio]" 
                                                       value="Maior idade (dia/mês/ano)">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerCriterio(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba 5: Configurações -->
                            <div class="tab-pane fade" id="config" role="tabpanel">
                                <h5 class="mb-3">Configurações do Processo</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status_global" class="form-label">Status do PSS *</label>
                                            <select class="form-select" id="status_global" name="status_global" required>
                                                <option value="rascunho">Rascunho</option>
                                                <option value="em_andamento">Em Andamento</option>
                                                <option value="suspenso">Suspenso</option>
                                                <option value="finalizado">Finalizado</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="permite_multiplas_inscricoes" 
                                               name="permite_multiplas_inscricoes">
                                        <label class="form-check-label" for="permite_multiplas_inscricoes">
                                            Permitir múltiplas inscrições (candidato pode se inscrever em mais de um cargo)
                                        </label>
                                    </div>
                                </div>
                                
                                <h6>Configurações Adicionais</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="exige_residencia" 
                                                   name="config[exige_residencia]">
                                            <label class="form-check-label" for="exige_residencia">
                                                Exigir comprovante de residência
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="permite_recursos" 
                                                   name="config[permite_recursos]" checked>
                                            <label class="form-check-label" for="permite_recursos">
                                                Permitir recursos (candidatos poderão contestar resultados)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="permite_impugnacao" 
                                                   name="config[permite_impugnacao]" checked>
                                            <label class="form-check-label" for="permite_impugnacao">
                                                Permitir impugnação do edital
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba 6: Auditoria -->
                            <div class="tab-pane fade" id="auditoria" role="tabpanel">
                                <h5 class="mb-3">Informações de Auditoria</h5>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Novo PSS:</strong> O histórico de auditoria será criado após o primeiro salvamento.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Informações de Criação</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Usuário:</strong> <?= $_SESSION['admin_nome'] ?? 'Administrador' ?></p>
                                                <p><strong>Data:</strong> <?= date('d/m/Y H:i:s') ?></p>
                                                <p><strong>IP:</strong> <?= $_SERVER['REMOTE_ADDR'] ?? 'N/A' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Histórico de Alterações</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Nenhuma alteração registrada ainda.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #666;
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
}

.nav-tabs .nav-link:hover {
    border-color: #2E7D32;
    color: #2E7D32;
}

.nav-tabs .nav-link.active {
    color: #2E7D32;
    border-color: #2E7D32;
    background: none;
    font-weight: 600;
}

.etapa-card, .cargo-card, .criterio-card {
    border-left: 4px solid #2E7D32;
}

.btn-success {
    background-color: #2E7D32;
    border-color: #2E7D32;
}

.btn-success:hover {
    background-color: #1B5E20;
    border-color: #1B5E20;
}

.badge.bg-primary {
    background-color: #2E7D32 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let etapaCount = 1;
    let cargoCount = 0;
    let criterioCount = 3;

    // Adicionar nova etapa
    document.getElementById('adicionarEtapa').addEventListener('click', function() {
        const container = document.getElementById('etapasContainer');
        const etapaHtml = `
            <div class="card mb-3 etapa-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar"></i> Etapa ${etapaCount + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerEtapa(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nome da Etapa *</label>
                            <input type="text" class="form-control" name="etapas[${etapaCount}][nome]" required 
                                   placeholder="Ex: Análise Documental">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="etapas[${etapaCount}][descricao]" 
                                   placeholder="Descrição da etapa">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Data/Hora de Início *</label>
                            <input type="datetime-local" class="form-control" name="etapas[${etapaCount}][dt_ini]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data/Hora de Fim *</label>
                            <input type="datetime-local" class="form-control" name="etapas[${etapaCount}][dt_fim]" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', etapaHtml);
        etapaCount++;
    });

    // Adicionar novo cargo
    document.getElementById('adicionarCargo').addEventListener('click', function() {
        const container = document.getElementById('cargosContainer');
        const cargoHtml = `
            <div class="card mb-3 cargo-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-briefcase"></i> Cargo ${cargoCount + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerCargo(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Cargo *</label>
                            <input type="text" class="form-control" name="cargos[${cargoCount}][nome]" required 
                                   placeholder="Ex: Auxiliar Administrativo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nível</label>
                            <select class="form-select" name="cargos[${cargoCount}][nivel]">
                                <option value="elementar">Elementar</option>
                                <option value="auxiliar">Auxiliar</option>
                                <option value="tecnico">Técnico</option>
                                <option value="superior">Superior</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label class="form-label">Vagas Totais *</label>
                            <input type="number" class="form-control" name="cargos[${cargoCount}][vagas_total]" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vagas PCD</label>
                            <input type="number" class="form-control" name="cargos[${cargoCount}][vagas_pcd]" min="0" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vagas PPP</label>
                            <input type="number" class="form-control" name="cargos[${cargoCount}][vagas_ppp]" min="0" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Salário Base</label>
                            <input type="number" class="form-control" name="cargos[${cargoCount}][salario_base]" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Carga Horária</label>
                            <input type="text" class="form-control" name="cargos[${cargoCount}][carga_horaria]" 
                                   placeholder="Ex: 40h semanais">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zona</label>
                            <select class="form-select" name="cargos[${cargoCount}][zona]">
                                <option value="urbana">Urbana</option>
                                <option value="rural">Rural</option>
                                <option value="ambas">Ambas</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Requisitos</label>
                        <textarea class="form-control" name="cargos[${cargoCount}][requisitos_texto]" rows="2" 
                                  placeholder="Requisitos necessários para o cargo..."></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cargoHtml);
        cargoCount++;
    });

    // Adicionar novo critério
    document.getElementById('adicionarCriterio').addEventListener('click', function() {
        const container = document.getElementById('criteriosContainer');
        const criterioHtml = `
            <div class="card mb-2 criterio-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">${criterioCount + 1}º</span>
                        <input type="text" class="form-control me-2" name="criterios[${criterioCount}][criterio]" 
                               placeholder="Digite o critério de desempate...">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerCriterio(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', criterioHtml);
        criterioCount++;
        atualizarNumeracaoCriterios();
    });

    // Salvar PSS
    document.getElementById('salvarPss').addEventListener('click', function() {
        const form = document.getElementById('formPss');
        if (form.checkValidity()) {
            form.submit();
        } else {
            form.reportValidity();
        }
    });
});

// Funções auxiliares
function removerEtapa(button) {
    button.closest('.etapa-card').remove();
}

function removerCargo(button) {
    button.closest('.cargo-card').remove();
}

function removerCriterio(button) {
    button.closest('.criterio-card').remove();
    atualizarNumeracaoCriterios();
}

function atualizarNumeracaoCriterios() {
    const criterios = document.querySelectorAll('.criterio-card');
    criterios.forEach((criterio, index) => {
        const badge = criterio.querySelector('.badge');
        badge.textContent = (index + 1) + 'º';
    });
}
</script>

