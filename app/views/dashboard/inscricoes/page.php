<?php $this->layout("dashboard/template") ?>
<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Gerenciamento de Inscrições</h2>
        <!--div class="btn-group-responsive">
            <button class="btn btn-primary">
                <i class="fas fa-download me-2"></i> Exportar
            </button>
            <button class="btn btn-outline-primary">
                <i class="fas fa-sync-alt me-2"></i> Atualizar
            </button>
        </div-->
    </div>
    <!-- Cards de Estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted fw-normal mb-2">Total de Candidatos</h6>
                            <h3 class="mb-0" id="total-candidatos">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-primary fa-2x"></i>
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
                            <h6 class="text-muted fw-normal mb-2">Deferidos</h6>
                            <h3 class="mb-0" id="deferidos">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
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
                            <h6 class="text-muted fw-normal mb-2">Indeferidos</h6>
                            <h3 class="mb-0" id="indeferidos">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-danger fa-2x"></i>
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
                            <h6 class="text-muted fw-normal mb-2">Pendentes</h6>
                            <h3 class="mb-0" id="pendentes">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- loading states -->
    <div id="loading-stats" class="text-center " style="display: block;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando estatísticas...</span>
        </div>
    </div>
    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Filtros de Busca</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="processoFilter" class="form-label">Processo Seletivo</label>
                    <select class="form-select" id="processoFilter" name="processo_id"
                        onchange="carregarCargos(this.value, 'cargoFilter')">
                        <option value="">Todos os processos</option>
                        <?php foreach ($processos as $processo): ?>
                            <option value="<?php echo $processo['id']; ?>">
                                <?php echo $processo['titulo']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3" style="width: 150px;">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">

                        <option value="all">Todas</option>
                        <option value="deferido">Deferido</option>
                        <option value="indeferido">Indeferido</option>
                        <option value="apta">Apta</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3" style="width: 200px;">
                    <label for="vagaFilter" class="form-label">Vaga</label>
                    <select class="form-select" id="vagaFilter">
                        <option value="all">Todos as vagas</option>
                        <option value="ampla">Ampla</option>
                        <option value="pcd">PCD</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3" style="width: 300px;">
                    <label for="cargoFilter" class="form-label">Cargo</label>
                    <select class="form-select" id="cargoFilter">
                        <option value="">Todos os cargos</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3" style="width: 100px;">
                    <label for="qtdInscricoesPorPaginaFilter" class="form-label">Inscrições</label>
                    <select class="form-select" id="qtdInscricoesPorPaginaFilter">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="searchFilter" class="form-label">Buscar por CPF</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Digite para buscar...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="searchFilterNome" class="form-label">Buscar por Nome</label>
                    <input type="text" class="form-control" id="searchFilterNome" name="searchFilterNome"
                        placeholder="Digite o nome para buscar...">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-secondary me-2" id="limparFiltros">
                    <i class="fas fa-times me-2"></i> Limpar Filtros
                </button>
                <button class="btn btn-primary" id="aplicarFiltros">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
    <!-- Tabela de Candidatos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Candidatos Inscritos</h5>
            <span class="text-muted small">Total: 0 candidatos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Inscrição</th>
                            <th>Candidato</th>
                            <th>CPF</th>
                            <th>Processo</th>
                            <th>Cargo</th>
                            <th>Data Inscrição</th>
                            <th>Status</th>
                            <!--th>Ações</th-->
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="pagination"></ul>
                </nav>
            </div>


        </div>
    </div>
</div>

<script>
    let limit = 10; // quantidade de itens por página
    let currentPage = 0;
    let totalItems = 0;

    async function carregarCargos(id, select_id, cargo_id = null) {
        const selectCargos = document.getElementById(select_id);
        if (!id) {
            selectCargos.innerHTML = '<option value="" disabled selected>Primeiro selecione o processo</option>';
            return;
        }

        selectCargos.innerHTML = '<option value="" disabled selected>Carregando cargos...</option>';

        try {
            carregarStatus(id);
            const response = await fetch(`/api/cargos/${id}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            selectCargos.innerHTML = '<option value="">Selecione o cargo</option>';
            if (data && Array.isArray(data)) {
                data.forEach(cargo => {
                    const option = document.createElement('option');
                    // option.value = cargo.id;
                    option.value = cargo.nome;
                    option.textContent = cargo.nome;
                    selectCargos.appendChild(option);
                });

                if (cargo_id) {
                    selectCargos.value = cargo_id;
                }



            } else {
                throw new Error('Formato de dados inválido');
            }
        } catch (error) {
            console.error('Erro ao carregar cargos:', error);
            selectCargos.innerHTML = '<option value="" disabled>Erro ao carregar cargos</option>';
        }
    }

    function carregarStatus(id) {
        const loadingElement = document.getElementById('loading-stats');
        const statsElements = {
            total: document.getElementById('total-candidatos'),
            deferidos: document.getElementById('deferidos'),
            indeferidos: document.getElementById('indeferidos'),
            pendentes: document.getElementById('pendentes')

        };
        // Mostra loading
        console.log(id)

        loadingElement.style.display = 'block';
        fetch(`/api/status/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(data)
                statsElements.total.textContent = formatarNumero(data.apta + data.indeferido + data.deferido);
                statsElements.deferidos.textContent = formatarNumero(data.deferido);
                statsElements.indeferidos.textContent = formatarNumero(data.indeferido);
                statsElements.pendentes.textContent = formatarNumero(data.apta);
                // animarContagem(statsElements, data);
                loadingElement.style.display = 'none';
            })
            .catch(error => {
                console.error('Erro ao carregar cidades:', error);
                selectCargos.innerHTML = '<option value="" disabled>Erro ao carregar cargos</option>';
                loadingElement.style.display = 'none';
            });
    }

    function formatarNumero(numero) {
        return new Intl.NumberFormat('pt-BR').format(numero);
    }


    function animarContagem(elements, data) {
        const duracao = 2000; // 2 segundos
        const fps = 60;
        const frameDuration = duracao / fps;

        Object.keys(elements).forEach(key => {
            const element = elements[key];
            const valorFinal = data[key] || 0;
            const valorInicial = parseInt(element.textContent.replace(/\./g, '')) || 0;

            let valorAtual = valorInicial;
            const incremento = (valorFinal - valorInicial) / fps;
            let frame = 0;

            const timer = setInterval(() => {
                frame++;
                valorAtual += incremento;

                if (frame >= fps) {
                    element.textContent = formatarNumero(valorFinal);
                    clearInterval(timer);
                } else {
                    element.textContent = formatarNumero(Math.round(valorAtual));
                }
            }, frameDuration);
        });
    }
    // console.log("pss", processo_id);
    // if (processo_id)
    // carregarStatus(2);
    async function carregaInscricoes(processo_id, cargo_id, statu, cpf, vagaTipo, nome) {
        console.log("carregaInscricoes", processo_id, cargo_id, statu, cpf, vagaTipo);
        let bodyContent = new FormData();
        bodyContent.append("process_id", processo_id);
        bodyContent.append("cargo_id", cargo_id);
        bodyContent.append("page", currentPage);
        bodyContent.append("limite", limit);
        bodyContent.append("status", statu);
        bodyContent.append("cpf", cpf);
        bodyContent.append("nome", nome);
        bodyContent.append("vagaTipo", vagaTipo);


        const tableBody = document.querySelector('.table-hover tbody');
        tableBody.innerHTML = '';

        await fetch("/api/inscricoes", {
            method: "POST",
            body: bodyContent,
            credentials: "same-origin",
            headers: {
                "Accept": "*/*",
                "X-CSRF-Token": getDashboardCsrfToken(),
            }
        }).then(response => {
            return response.json();
        }).then(

            data => {
                console.log(data);
                totalItems = data[0].total_registros ?? 0;

                if (data && Array.isArray(data)) {
                    data.forEach(inscricao => {
                        const row = document.createElement('tr');
                        row.classList.add(
                            'candidate-row'); // Add class for potential future styling/selection

                        // Construct cells based on expected data structure from API
                        row.innerHTML = `
                    <td><a href="/dashboard/inscricoes/detalhes/${inscricao.id}">${inscricao.protocolo || inscricao.id}</a></td>
                    <td>${inscricao.candidato_nome.toUpperCase()}</td>
                    <td>${inscricao.candidato_cpf}</td>
                    <td>${inscricao.pss_titulo}</td>
                    <td>${inscricao.cargo_nome}</td>
                    <td>${inscricao.inscricao_ini ? new Date(inscricao.inscricao_ini).toLocaleDateString() : ''}</td>
                    <td>

                    <span class="badge ${ inscricao.status == 'deferido'    ? 'bg-success'    : (inscricao.status == 'indeferido'        ? 'bg-danger'        : 'bg-warning')}">  ${inscricao.status}</span>
                   
                    </td>
                    <!--td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary action-btn" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-success action-btn" title="Aprovar">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-outline-danger action-btn" title="Reprovar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td-->
                `;
                        tableBody.appendChild(row);
                    });
                    // Update total count
                    document.querySelector('.card-header .text-muted').textContent =
                        `Total: ${data.length} / ${totalItems} candidatos`;
                } else {
                    throw new Error('Formato de dados inválido recebido da API.');
                }

                // carregarStatus(processo_id);
                renderPagination();
            }
        );


    }
</script>



<script>
    let processo_id; // = sessionStorage.getItem('processo_id') ?? 1;
    let cargo_id; //= sessionStorage.getItem('cargo_id') ?? 1;
    let status_insc; // = sessionStorage.getItem('status') ?? 'apta';
    let vagaTipo;
    let cpf;
    let nome;
    let vagaTipo1;


    // processo_id = sessionStorage.getItem('processo_id');
    // cargo_id = sessionStorage.getItem('cargo_id');
    // status_insc = sessionStorage.getItem('status');
    // cpf = sessionStorage.getItem('cpf');
    // currentPage = parseInt(sessionStorage.getItem('currentPage')) || 0;
    // limit = parseInt(sessionStorage.getItem('limit')) || 10;


    document.addEventListener('DOMContentLoaded', function() {
        // Aplicar filtros e buscar inscrições da API
        document.getElementById('aplicarFiltros').addEventListener('click', function() {
            currentPage = 0;
            totalItems = 0;
            sessionStorage.clear();
            const processoFilter = document.getElementById('processoFilter');
            const cargoFilter = document.getElementById('cargoFilter');
            const statusFilter = document.getElementById('statusFilter');
            const cpfFilter = document.getElementById('searchFilter');
            const nomeFilter = document.getElementById('searchFilterNome');
            const qtdInscricoesPorPaginaFilter = document.getElementById('qtdInscricoesPorPaginaFilter')
            const vagaTipoInput = document.getElementById('vagaFilter')

            processo_id = processoFilter.value;
            cargo_id = cargoFilter.value;
            status_insc = statusFilter.value;
            cpf = cpfFilter.value;
            nome = nomeFilter.value;
            limit = qtdInscricoesPorPaginaFilter.value;
            vagaTipo1 = vagaTipoInput.value;
            console.log("Cargos id", cargo_id)
            // Save filters to session storage
            sessionStorage.setItem('processo_id', processo_id);
            sessionStorage.setItem('cargo_id', cargo_id);
            sessionStorage.setItem('status', status_insc);
            sessionStorage.setItem('cpf', cpf);
            sessionStorage.setItem('nome', nome);
            sessionStorage.setItem('limit', limit);
            sessionStorage.setItem('vagaFilter', vagaTipo1);


            carregaInscricoes(processo_id, cargo_id, status_insc, cpf, vagaTipo1, nome);
            // if (processo_id)
            //     carregarStatus(processo_id)

        });

        // Limpar filtros
        document.getElementById('limparFiltros').addEventListener('click', function() {
            sessionStorage.clear();
            // Clear filters from session storage
            sessionStorage.removeItem('processo_id');
            sessionStorage.removeItem('cargo_id');
            sessionStorage.removeItem('status');
            sessionStorage.removeItem('cpf')
            sessionStorage.removeItem('nome')
            sessionStorage.removeItem('currentPage');
            sessionStorage.removeItem('vagaTipo');
            currentPage = 0

            document.getElementById('processoFilter').value = '';
            document.getElementById('statusFilter').value = 'apta';
            document.getElementById('cargoFilter').value = '';
            document.getElementById('searchFilter').value = '';
            document.getElementById('searchFilterNome').value = '';
            document.getElementById('vagaFilter').value = 'all';
            document.getElementById('qtdInscricoesPorPaginaFilter').value = 10;

            // Resetting the cargo filter to its default state
            const cargoFilterSelect = document.getElementById('cargoFilter');
            cargoFilterSelect.innerHTML = '<option value="">Todos os cargos</option>';
        });

        // Load filters from session storage on page load
        // Query ?processo_id= (ex.: vindo do dashboard) tem prioridade e atualiza o select
        const urlParams = new URLSearchParams(window.location.search);
        const processoIdQuery = urlParams.get('processo_id');
        if (processoIdQuery !== null && processoIdQuery !== '') {
            sessionStorage.setItem('processo_id', processoIdQuery);
            sessionStorage.removeItem('cargo_id');
        }

        processo_id = sessionStorage.getItem('processo_id') || 1;
        cargo_id = sessionStorage.getItem('cargo_id') || 1;
        status_insc = sessionStorage.getItem('status') || 'apta';
        cpf = sessionStorage.getItem('cpf');
        nome = sessionStorage.getItem('nome');
        currentPage = parseInt(sessionStorage.getItem('currentPage')) || 0;
        limit = parseInt(sessionStorage.getItem('limit')) || 10;
        vagaTipo1 = sessionStorage.getItem('vagaFilter') || 'all';



        if (processo_id) {
            document.getElementById('processoFilter').value = processo_id;
            // Optionally re-trigger carregarCargos if needed, but it might be handled by the initial load
            carregarCargos(processo_id, 'cargoFilter', cargo_id)
        }

        if (vagaTipo1) {
            document.getElementById('vagaFilter').value = vagaTipo1;
        }
        if (status_insc) {
            document.getElementById('statusFilter').value = status_insc;
        }
        if (cpf) {
            document.getElementById('searchFilter').value = cpf;
        }
        if (nome) {
            document.getElementById('searchFilterNome').value = nome;
        }
        if (limit) {
            document.getElementById('qtdInscricoesPorPaginaFilter').value = limit;
        }

        // Apply filters if they were loaded from session storage
        // A função agora é chamada incondicionalmente para garantir que os dados sejam carregados na visita à página.
        carregaInscricoes(processo_id, cargo_id, status_insc, cpf, vagaTipo1, nome);
        // });

        // Existing code for deferir/indeferir and batch actions remains here...
        // Ações de deferir/indeferir
        document.querySelectorAll('.btn-outline-success, .btn-outline-danger').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const nome = row.cells[1].textContent;
                const isAprovar = this.classList.contains('btn-outline-success');

                if (isAprovar) {
                    if (confirm(`Deseja deferir a inscrição de ${nome}?`)) {
                        row.cells[6].innerHTML =
                            '<span class="badge bg-success status-badge">Deferido</span>';
                    }
                } else {
                    if (confirm(`Deseja indeferir a inscrição de ${nome}?`)) {
                        row.cells[6].innerHTML =
                            '<span class="badge bg-danger status-badge">Indeferido</span>';
                    }
                }
            });
        });

        // Ações de deferir/indeferir
        document.querySelectorAll('.btn-outline-success, .btn-outline-danger').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const nome = row.cells[1].textContent;
                const isAprovar = this.classList.contains('btn-outline-success');

                if (isAprovar) {
                    if (confirm(`Deseja deferir a inscrição de ${nome}?`)) {
                        row.cells[6].innerHTML =
                            '<span class="badge bg-success status-badge">Deferido</span>';
                    }
                } else {
                    if (confirm(`Deseja indeferir a inscrição de ${nome}?`)) {
                        row.cells[6].innerHTML =
                            '<span class="badge bg-danger status-badge">Indeferido</span>';
                    }
                }
            });
        });

    });

    // Helper function to determine badge class based on status
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Deferido':
                return 'bg-success';
            case 'Indeferido':
                return 'bg-danger';
            case 'apto':
                return 'bg-warning';
            default:
                return 'bg-secondary';
        }
    }
</script>

<script>
    function renderPagination() {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        const totalPages = Math.ceil(totalItems / limit);
        const maxVisible = 5; // quantas páginas mostrar de cada vez
        let startPage = Math.max(currentPage - Math.floor(maxVisible / 2), 1);
        let endPage = startPage + maxVisible - 1;

        if (endPage > totalPages) {
            endPage = totalPages;
            startPage = Math.max(endPage - maxVisible + 1, 1);
        }

        // Botão "Anterior"
        const prevLi = document.createElement("li");
        prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
        prevLi.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
        prevLi.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                // carregaInscricoes(1, 18);

                sessionStorage.setItem('currentPage', currentPage);
                carregaInscricoes(processo_id, cargo_id, status_insc, cpf, vagaTipo1, nome);
            }
        });
        pagination.appendChild(prevLi);

        // Se não começa na página 1, mostrar botão 1 + "..."
        if (startPage > 1) {
            pagination.appendChild(createPageItem(1));
            if (startPage > 2) {
                pagination.appendChild(createDots());
            }
        }

        // Números de página visíveis
        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(createPageItem(i));
        }

        // Se não termina na última página, mostrar "..." + última página
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination.appendChild(createDots());
            }
            pagination.appendChild(createPageItem(totalPages));
        }

        // Botão "Próxima"
        const nextLi = document.createElement("li");
        nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
        nextLi.innerHTML = `<a class="page-link" href="#">Próxima</a>`;
        nextLi.addEventListener("click", () => {
            if (currentPage < totalPages) {
                currentPage++;

                sessionStorage.setItem('currentPage', currentPage);
                carregaInscricoes(processo_id, cargo_id, status_insc, cpf, vagaTipo1, nome);
            }
        });
        pagination.appendChild(nextLi);
    }

    function createDots() {
        const li = document.createElement("li");
        li.className = "page-item disabled";
        li.innerHTML = `<a class="page-link" href="#">...</a>`;
        return li;
    }

    function createPageItem(page) {
        const li = document.createElement("li");
        li.className = `page-item ${page === currentPage ? "active" : ""}`;
        li.innerHTML = `<a class="page-link" href="#">${page}</a>`;
        li.addEventListener("click", () => {
            currentPage = page;
            sessionStorage.setItem('currentPage', currentPage);
            carregaInscricoes(processo_id, cargo_id, status_insc, cpf, vagaTipo1, nome);
        });
        return li;
    }
</script>