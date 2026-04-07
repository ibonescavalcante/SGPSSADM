<?php $this->layout("dashboard/template") ?>

<!-- Navbar -->



<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Gerenciamento de Processos</h2>
        <button class="btn btn-primary d-flex align-items-center"
            onclick="window.location.href='/dashboard/processos/novo'">
            <i class="fas fa-plus me-2"></i> Novo Processo
        </button>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Total de Processos</h6>
                            <h3 class="mb-0">24</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Processos Ativos</h6>
                            <h3 class="mb-0">12</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-play-circle text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Processos Encerrados</h6>
                            <h3 class="mb-0">8</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-stop-circle text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Rascunhos</h6>
                            <h3 class="mb-0">4</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Filtros de Pesquisa</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos os status</option>
                        <option value="ativo">Ativo</option>
                        <option value="rascunho">Rascunho</option>
                        <option value="pausado">Pausado</option>
                        <option value="encerrado">Encerrado</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="secretariaFilter" class="form-label">Secretaria</label>
                    <select class="form-select" id="secretariaFilter">
                        <option value="">Todas as secretarias</option>
                        <option value="educacao">Educação</option>
                        <option value="saude">Saúde</option>
                        <option value="administracao">Administração</option>
                        <option value="infraestrutura">Infraestrutura</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="anoFilter" class="form-label">Ano</label>
                    <select class="form-select" id="anoFilter">
                        <option value="">Todos os anos</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="searchFilter" class="form-label">Pesquisar</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" id="searchFilter"
                            placeholder="Pesquisar por número, título ou descrição...">
                    </div>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="d-flex w-100 gap-2">
                        <button class="btn btn-outline-secondary w-50" id="limparFiltros">
                            <i class="fas fa-times me-2"></i> Limpar
                        </button>
                        <button class="btn btn-primary w-50" id="aplicarFiltros">
                            <i class="fas fa-filter me-2"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Processos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Todos os Processos</h5>
            <span class="text-muted small">Total: 24 processos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Título</th>
                            <th>Secretaria</th>
                            <th>Período de Inscrição</th>
                            <th>Vagas</th>
                            <th>Inscrições</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="processo-row">
                            <td>001/2023</td>
                            <td>Professor de Matemática</td>
                            <td>Educação</td>
                            <td>01/08/2023 - 15/08/2023</td>
                            <td>15</td>
                            <td>243</td>
                            <td>
                                <span class="badge bg-success status-badge">Ativo</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger action-btn" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="processo-row">
                            <td>002/2023</td>
                            <td>Assistente Administrativo</td>
                            <td>Administração</td>
                            <td>05/08/2023 - 20/08/2023</td>
                            <td>8</td>
                            <td>187</td>
                            <td>
                                <span class="badge bg-success status-badge">Ativo</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger action-btn" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="processo-row">
                            <td>003/2023</td>
                            <td>Técnico em Enfermagem</td>
                            <td>Saúde</td>
                            <td>10/08/2023 - 25/08/2023</td>
                            <td>12</td>
                            <td>321</td>
                            <td>
                                <span class="badge bg-warning status-badge">Rascunho</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger action-btn" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="processo-row">
                            <td>004/2022</td>
                            <td>Engenheiro Civil</td>
                            <td>Infraestrutura</td>
                            <td>15/09/2022 - 30/09/2022</td>
                            <td>5</td>
                            <td>89</td>
                            <td>
                                <span class="badge bg-danger status-badge">Encerrado</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger action-btn" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="processo-row">
                            <td>005/2022</td>
                            <td>Médico Clínico Geral</td>
                            <td>Saúde</td>
                            <td>20/10/2022 - 05/11/2022</td>
                            <td>7</td>
                            <td>154</td>
                            <td>
                                <span class="badge bg-secondary status-badge">Pausado</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger action-btn" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando 5 de 24 processos
                </div>

                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">4</a></li>
                        <li class="page-item"><a class="page-link" href="#">5</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Próxima</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Novo Processo -->
<div class="modal fade" id="novoProcessoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Criar Novo Processo Seletivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numeroEdital" class="form-label">Número do Edital *</label>
                            <input type="text" class="form-control" id="numeroEdital" placeholder="Ex: 001/2023"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="secretaria" class="form-label">Secretaria/Órgão *</label>
                            <select class="form-select" id="secretaria" required>
                                <option value="">Selecione uma secretaria</option>
                                <option value="educacao">Secretaria de Educação</option>
                                <option value="saude">Secretaria de Saúde</option>
                                <option value="administracao">Secretaria de Administração</option>
                                <option value="infraestrutura">Secretaria de Infraestrutura</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="tituloProcesso" class="form-label">Título do Processo Seletivo *</label>
                            <input type="text" class="form-control" id="tituloProcesso"
                                placeholder="Ex: Processo Seletivo para Professores" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dataInicio" class="form-label">Data de Início das Inscrições *</label>
                            <input type="date" class="form-control" id="dataInicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="dataFim" class="form-label">Data de Término das Inscrições *</label>
                            <input type="date" class="form-control" id="dataFim" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descricao" class="form-label">Descrição do Processo</label>
                            <textarea class="form-control" id="descricao" rows="3"
                                placeholder="Descreva o objetivo e características deste processo seletivo"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Criar Processo</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal de novo processo
        const novoProcessoModal = new bootstrap.Modal(document.getElementById('novoProcessoModal'));

        document.getElementById('btnNovoProcesso').addEventListener('click', function() {
            novoProcessoModal.show();
        });

        // Aplicar filtros
        document.getElementById('aplicarFiltros').addEventListener('click', function() {
            const status = document.getElementById('statusFilter').value;
            const secretaria = document.getElementById('secretariaFilter').value;
            const ano = document.getElementById('anoFilter').value;
            const search = document.getElementById('searchFilter').value.toLowerCase();

            const rows = document.querySelectorAll('.processo-row');
            let visibleCount = 0;

            rows.forEach(row => {
                let show = true;
                const statusText = row.cells[6].textContent.toLowerCase();
                const secretariaText = row.cells[2].textContent.toLowerCase();
                const numeroText = row.cells[0].textContent;
                const tituloText = row.cells[1].textContent.toLowerCase();

                // Aplicar filtros
                if (status && status === 'ativo' && !statusText.includes('ativo')) show = false;
                if (status && status === 'rascunho' && !statusText.includes('rascunho')) show =
                    false;
                if (status && status === 'pausado' && !statusText.includes('pausado')) show =
                    false;
                if (status && status === 'encerrado' && !statusText.includes('encerrado'))
                    show = false;

                if (secretaria && !secretariaText.includes(secretaria)) show = false;
                if (ano && !numeroText.includes(ano)) show = false;
                if (search && !tituloText.includes(search) && !numeroText.includes(search))
                    show = false;

                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            // Atualizar contador
            document.querySelector('.card-header .text-muted').textContent =
                `Total: ${visibleCount} processos`;
            document.querySelector('.text-muted').textContent =
                `Mostrando ${visibleCount} de ${visibleCount} processos`;
        });

        // Limpar filtros
        document.getElementById('limparFiltros').addEventListener('click', function() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('secretariaFilter').value = '';
            document.getElementById('anoFilter').value = '';
            document.getElementById('searchFilter').value = '';

            const rows = document.querySelectorAll('.processo-row');
            rows.forEach(row => {
                row.style.display = '';
            });

            document.querySelector('.card-header .text-muted').textContent = 'Total: 24 processos';
            document.querySelector('.text-muted').textContent = 'Mostrando 5 de 24 processos';
        });

        // Ações dos botões
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const numero = row.cells[0].textContent;
                const titulo = row.cells[1].textContent;

                if (this.title === 'Visualizar') {
                    alert(`Visualizando processo: ${numero} - ${titulo}`);
                } else if (this.title === 'Editar') {
                    alert(`Editando processo: ${numero} - ${titulo}`);
                } else if (this.title === 'Excluir') {
                    if (confirm(
                            `Tem certeza que deseja excluir o processo ${numero} - ${titulo}?`
                        )) {
                        alert(`Processo ${numero} excluído com sucesso!`);
                    }
                }
            });
        });
    });
</script>