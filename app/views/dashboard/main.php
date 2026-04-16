<?php $this->layout("dashboard/template") ?>
<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
      
       
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Processos Ativos</h6>
                            <h3 class="mb-0">1</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Candidatos</h6>
                            <h3 class="mb-0">15.254</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Inscrições</h6>
                            <h3 class="mb-0">15.254</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Documentos Pendentes</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div-->
    </div>

    <!-- Processos em Destaque -->
    <div class="row">
        <div class="col-lg-12 mb-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Processos em Andamento</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center processo-card">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">PROCESSO SELETIVO SIMPLIFICADO (PSS) - EDITAL Nº 001/2025 -PMP</h6>
                                <p class="mb-0 text-muted small">Inscrições: 01/08/2023 a 15/08/2023</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success status-badge">Ativo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
    </div>

    <!-- Tabela de Inscrições Recentes -->
    <!--div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inscrições Recentes</h5>
            <div>
                <button disabled class="btn btn-sm btn-outline-primary me-2">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <button disabled class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Candidato</th>
                            <th>CPF</th>
                            <th>Processo</th>
                            <th>Data Inscrição</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div-->
</div>
</div>
</div>

<!-- Modal de Login (exemplo) -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Acesso ao Sistema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Lembrar-me</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="#">Esqueci minha senha</a>
            </div>
        </div>
    </div>
</div>