<?php $this->layout("admin/template") ?>

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
                        <a href="/admin/pss" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
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
                    </ul>

                    <!-- Formulário Principal -->
                    <form id="formPss" method="POST" action="/admin/pss/salvar">
                        <div class="tab-content" id="pssTabContent">
                            
                            <!-- Aba 1: Informações Básicas -->
                            <div class="tab-pane fade show active" id="basicas" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Título do PSS *</label>
                                            <input type="text" class="form-control" name="titulo" required 
                                                   placeholder="Ex: PSS Prefeitura Municipal - Elementar e Auxiliar">
                                            <div class="form-text">Nome que aparecerá nos editais e documentos oficiais</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Secretaria Responsável</label>
                                            <input type="text" class="form-control" name="secretaria" 
                                                   placeholder="Ex: Secretaria Municipal de Administração">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Descrição</label>
                                            <textarea class="form-control" name="descricao" rows="3" 
                                                      placeholder="Descrição detalhada do processo seletivo..."></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ano de Exercício *</label>
                                            <input type="number" class="form-control" name="ano_exercicio" 
                                                   value="<?= date('Y') ?>" min="2024" max="2030" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status_global">
                                                <option value="em_andamento">Em Andamento</option>
                                                <option value="suspenso">Suspenso</option>
                                                <option value="finalizado">Finalizado</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permite_multiplas_inscricoes" value="1">
                                                <label class="form-check-label">
                                                    Permitir múltiplas inscrições
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba 2: Etapas -->
                            <div class="tab-pane fade" id="etapas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Cronograma do Processo</h5>
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
                                <h5>Configurações Avançadas</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Documentos Obrigatórios</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="documentos_obrigatorios[]" value="rg" checked>
                                                    <label class="form-check-label">RG</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="documentos_obrigatorios[]" value="cpf" checked>
                                                    <label class="form-check-label">CPF</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="documentos_obrigatorios[]" value="comprovante_residencia" checked>
                                                    <label class="form-check-label">Comprovante de Residência</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="documentos_obrigatorios[]" value="escolaridade">
                                                    <label class="form-check-label">Comprovante de Escolaridade</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Outras Configurações</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Taxa de Inscrição</label>
                                                    <input type="number" class="form-control" name="taxa_inscricao" step="0.01" min="0" value="0">
                                                </div>
                                                
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permite_recurso" value="1" checked>
                                                    <label class="form-check-label">Permitir recursos</label>
                                                </div>
                                                
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="publicar_resultado" value="1" checked>
                                                    <label class="form-check-label">Publicar resultado automaticamente</label>
                                                </div>
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
                                   placeholder="Ex: Análise de Documentos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="etapas[${etapaCount}][descricao]" 
                                   placeholder="Descrição da etapa...">
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

<style>
/* Estilos específicos para a página Criar Novo PSS */
.page-content {
    background: var(--color-gray-50);
    min-height: calc(100vh - var(--admin-header-height));
}

/* Header da Página */
.page-header {
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
    color: white;
    padding: 2rem;
    margin: 0;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.header-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    max-width: 1200px;
    margin: 0 auto;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
}

.header-text h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
}

.header-text p {
    margin: 0;
    font-size: 1.125rem;
    opacity: 0.9;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

/* Botões */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all var(--transition-fast);
    white-space: nowrap;
}

.btn-outline {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    text-decoration: none;
}

.btn-primary {
    background: rgba(255, 255, 255, 0.9);
    color: var(--color-primary);
    border: 2px solid transparent;
}

.btn-primary:hover {
    background: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Navegação por Abas */
.tabs-navigation {
    background: white;
    border-bottom: 1px solid var(--color-border);
    padding: 0 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: var(--shadow-sm);
}

.tabs-container {
    display: flex;
    max-width: 1200px;
    margin: 0 auto;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.tabs-container::-webkit-scrollbar {
    display: none;
}

.tab-button {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    transition: all var(--transition-fast);
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    min-width: fit-content;
}

.tab-button:hover {
    background: var(--color-gray-50);
}

.tab-button.active {
    border-bottom-color: var(--color-primary);
    background: var(--color-gray-50);
}

.tab-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-500);
    transition: color var(--transition-fast);
}

.tab-button.active .tab-icon {
    color: var(--color-primary);
}

.tab-content {
    display: flex;
    flex-direction: column;
}

.tab-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--color-text);
    margin-bottom: 0.125rem;
}

.tab-subtitle {
    font-size: 0.75rem;
    color: var(--color-text-muted);
}

/* Container Principal */
.content-container {
    max-width: calc(1200px - var(--admin-sidebar-width)); /* Ajusta largura máxima */
    margin: 0 auto 0 var(--admin-sidebar-width); /* Adiciona margem à esquerda */
    padding: 2rem;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    align-items: start;
}

/* Painéis das Abas */
.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

/* Seções do Formulário */
.form-section {
    background: var(--color-white);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
}

.section-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--color-border);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: var(--color-primary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title::before {
    content: '';
    width: 4px;
    height: 24px;
    background: var(--color-primary);
    border-radius: 2px;
}

.section-subtitle {
    margin: 0;
    color: var(--color-text-light);
    font-size: 1rem;
}

/* Grid de Formulário */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.col-span-full {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--color-text);
    font-size: 0.875rem;
}

.form-label.required::after {
    content: ' *';
    color: var(--color-error);
}

.form-input,
.form-textarea,
.form-select {
    padding: 0.875rem 1rem;
    border: 2px solid var(--color-border);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all var(--transition-fast);
    background: var(--color-white);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-help {
    font-size: 0.75rem;
    color: var(--color-text-muted);
    margin-top: 0.25rem;
}

/* Sidebar de Dicas */
.sidebar {
    position: sticky;
    top: 120px;
}

.tips-card {
    background: var(--color-white);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
}

.tips-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--color-border);
}

.tips-icon {
    width: 32px;
    height: 32px;
    background: var(--color-primary);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.tips-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
    color: var(--color-text);
}

.info-card {
    background: rgba(33, 150, 243, 0.05);
    border: 1px solid rgba(33, 150, 243, 0.2);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.info-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.info-icon {
    color: var(--color-info);
    font-size: 1rem;
}

.info-title {
    font-weight: 600;
    color: var(--color-info);
    margin: 0;
    font-size: 0.875rem;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    padding: 0.25rem 0;
    font-size: 0.875rem;
    color: var(--color-text-light);
    position: relative;
    padding-left: 1rem;
}

.info-list li::before {
    content: '•';
    color: var(--color-info);
    position: absolute;
    left: 0;
}

/* Botão Próximo */
.next-button {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    box-shadow: var(--shadow-lg);
    transition: all var(--transition-fast);
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.next-button:hover {
    background: var(--color-primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Responsividade */
@media (max-width: 1024px) {
    .content-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .sidebar {
        position: static;
        order: -1;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem 1rem 2rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1.5rem;
        align-items: stretch;
    }
    
    .header-info {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .header-text h1 {
        font-size: 2rem;
    }
    
    .tabs-navigation {
        padding: 0 1rem;
    }
    
    .content-container {
        padding: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .next-button {
        bottom: 1rem;
        right: 1rem;
        left: 1rem;
        border-radius: 12px;
        justify-content: center;
    }
}
</style>

<!-- Header da Página -->
<div class="page-header">
    <div class="header-content">
        <div class="header-info">
            <div class="header-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="header-text">
                <h1>Criar Novo PSS</h1>
                <p>Configure todos os parâmetros do processo seletivo</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/pss" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <button type="button" class="btn btn-primary" id="salvarPss">
                <i class="fas fa-save"></i>
                Salvar PSS
            </button>
        </div>
    </div>
</div>

<!-- Navegação por Abas -->
<div class="tabs-navigation">
    <div class="tabs-container">
        <button class="tab-button active" data-tab="basicas">
            <div class="tab-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Informações Básicas</span>
                <span class="tab-subtitle">Dados gerais do PSS</span>
            </div>
        </button>
        
        <button class="tab-button" data-tab="etapas">
            <div class="tab-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Etapas</span>
                <span class="tab-subtitle">Cronograma do processo</span>
            </div>
        </button>
        
        <button class="tab-button" data-tab="cargos">
            <div class="tab-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Cargos/Vagas</span>
                <span class="tab-subtitle">Posições disponíveis</span>
            </div>
        </button>
        
        <button class="tab-button" data-tab="criterios">
            <div class="tab-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Critérios</span>
                <span class="tab-subtitle">Regras de desempate</span>
            </div>
        </button>
        
        <button class="tab-button" data-tab="configuracoes">
            <div class="tab-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Configurações</span>
                <span class="tab-subtitle">Opções avançadas</span>
            </div>
        </button>
        
        <button class="tab-button" data-tab="auditoria">
            <div class="tab-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="tab-content">
                <span class="tab-title">Auditoria</span>
                <span class="tab-subtitle">Histórico de alterações</span>
            </div>
        </button>
    </div>
</div>

<!-- Container Principal -->
<div class="content-container">
    <!-- Formulário Principal -->
    <div class="main-form">
        <form id="formPss" method="POST" action="/admin/pss/salvar">
            
            <!-- Aba: Informações Básicas -->
            <div class="tab-panel active" id="tab-basicas">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            Dados Principais
                        </h3>
                        <p class="section-subtitle">Informações básicas que identificam o processo seletivo</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <label for="titulo" class="form-label required">Título do PSS</label>
                            <input type="text" class="form-input" id="titulo" name="titulo" required 
                                   placeholder="Ex: PSS Prefeitura Municipal - Elementar e Auxiliar">
                            <div class="form-help">Nome que aparecerá nos editais e documentos oficiais</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="ano_exercicio" class="form-label required">Ano de Exercício</label>
                            <input type="number" class="form-input" id="ano_exercicio" name="ano_exercicio" 
                                   min="2024" max="2030" value="2025" required>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <label for="secretaria" class="form-label">Secretaria Responsável</label>
                            <input type="text" class="form-input" id="secretaria" name="secretaria" 
                                   placeholder="Ex: Secretaria Municipal de Administração">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-textarea" id="descricao" name="descricao" rows="4" 
                                      placeholder="Descrição detalhada do processo seletivo..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Etapas -->
            <div class="tab-panel" id="tab-etapas">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">Etapas do Processo Seletivo</h3>
                        <p class="section-subtitle">Defina o cronograma completo do processo</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <p>Conteúdo das etapas será implementado aqui...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Cargos/Vagas -->
            <div class="tab-panel" id="tab-cargos">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">Cargos e Vagas</h3>
                        <p class="section-subtitle">Configure as posições disponíveis no processo</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <p>Conteúdo dos cargos será implementado aqui...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Critérios -->
            <div class="tab-panel" id="tab-criterios">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">Critérios de Desempate</h3>
                        <p class="section-subtitle">Defina a ordem de prioridade para desempate</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <p>Conteúdo dos critérios será implementado aqui...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Configurações -->
            <div class="tab-panel" id="tab-configuracoes">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">Configurações do Processo</h3>
                        <p class="section-subtitle">Opções avançadas e regras específicas</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <p>Conteúdo das configurações será implementado aqui...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Auditoria -->
            <div class="tab-panel" id="tab-auditoria">
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">Auditoria e Histórico</h3>
                        <p class="section-subtitle">Registro de alterações e atividades</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-full">
                            <p>Conteúdo da auditoria será implementado aqui...</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
    
    <!-- Sidebar de Dicas -->
    <div class="sidebar">
        <div class="tips-card">
            <div class="tips-header">
                <div class="tips-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4 class="tips-title">Dicas</h4>
            </div>
            
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-info-circle info-icon"></i>
                    <h5 class="info-title">Informações Importantes</h5>
                </div>
                <ul class="info-list">
                    <li>O título será exibido publicamente</li>
                    <li>Ano de exercício define a validade</li>
                    <li>Secretaria responsável pelo processo</li>
                    <li>Descrição aparece no edital</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Botão Próximo -->
<button type="button" class="next-button" id="proximoBtn">
    Próximo
    <i class="fas fa-arrow-right"></i>
</button>

<script>
// Navegação por abas
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all buttons and panels
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));
            
            // Add active class to clicked button and corresponding panel
            this.classList.add('active');
            document.getElementById(`tab-${targetTab}`).classList.add('active');
        });
    });
    
    // Botão próximo
    document.getElementById('proximoBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.tab-button.active');
        const nextTab = activeTab.nextElementSibling;
        
        if (nextTab && nextTab.classList.contains('tab-button')) {
            nextTab.click();
        }
    });
});
</script>

