<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo PSS - Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Montserrat', sans-serif; 
            background: #f8f9fa;
        }
        .nav-tabs .nav-link.active {
            color: #2E7D32;
            border-color: #2E7D32;
            font-weight: 600;
        }
        .btn-success {
            background-color: #2E7D32;
            border-color: #2E7D32;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1 class="mb-0">
                            <i class="fas fa-plus-circle"></i>
                            Criar Novo PSS - Versão Debug
                        </h1>
                        <p class="mb-0">Se você está vendo esta tela, o template engine está funcionando!</p>
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
                        </ul>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content" id="pssTabContent">
                            
                            <!-- Aba 1: Informações Básicas -->
                            <div class="tab-pane fade show active" id="basicas" role="tabpanel">
                                <div class="alert alert-success">
                                    <h4><i class="fas fa-check-circle"></i> Sucesso!</h4>
                                    <p>A interface com abas está funcionando corretamente!</p>
                                </div>
                                
                                <form>
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
                                    
                                    <button type="button" class="btn btn-success">
                                        <i class="fas fa-save"></i> Salvar Informações Básicas
                                    </button>
                                </form>
                            </div>

                            <!-- Aba 2: Etapas -->
                            <div class="tab-pane fade" id="etapas" role="tabpanel">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Importante:</strong> A etapa de inscrição é obrigatória e sempre será a primeira.
                                </div>
                                
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-plus"></i> Etapa 1: Inscrição (Obrigatória)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Data/Hora de Início *</label>
                                                <input type="datetime-local" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Data/Hora de Fim *</label>
                                                <input type="datetime-local" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-success mt-3">
                                    <i class="fas fa-plus"></i> Adicionar Nova Etapa
                                </button>
                            </div>

                            <!-- Aba 3: Cargos/Vagas -->
                            <div class="tab-pane fade" id="cargos" role="tabpanel">
                                <div class="alert alert-warning">
                                    <i class="fas fa-briefcase"></i>
                                    <strong>Cargos e Vagas:</strong> Adicione os cargos disponíveis no processo seletivo.
                                </div>
                                
                                <button type="button" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Adicionar Cargo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

