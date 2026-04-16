<?php $this->layout("dashboard/template") ?>
<?php
// echo ("<pre>");
// var_dump($processos);
// // var_dump($pontuacao);
// echo ("<pre>");
// die;
?>
<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Gerenciamento de Recursos</h2>
    </div>

    <!-- loading states -->
    <!--div id="loading-stats" class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando estatísticas...</span>
        </div>
    </div-->
    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Filtros de Busca</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="processoFilter" class="form-label">Processo Seletivo</label>
                    <select class="form-select" id="processoFilter" name="processo_id">
                        <option value="">Todos os processos</option>
                        <?php foreach ($processos as $processo): ?>
                        <option value="<?php echo $processo['id']; ?>">
                            <?php echo $processo['titulo']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="todos">Todos</option>
                        <option value="aberto">Aberto</option>
                        <option value="deferido">Deferido</option>
                        <option value="indeferido">Indeferido</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary me-2 w-100" id="limparFiltros">
                        <i class="fas fa-times me-2"></i> Limpar Filtros
                    </button>
                    <button class="btn btn-primary w-100" id="aplicarFiltros">
                        <i class="fas fa-filter me-2"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

    </div>
    <!-- Tabela de Candidatos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recursos</h5>
            <span class="text-muted small">Total: 0 candidatos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Candidato</th>
                            <th>Inscrição</th>
                            <th>Status</th>
                            <th>Ações</th>
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
let limit = 50; // quantidade de itens por página
let currentPage = 0;
let totalItems = 0;

async function carregar_recursos(processo_id, status) {
    console.log("carregar_recursos", processo_id, status);
    let bodyContent = new FormData();
    bodyContent.append("process_id", processo_id);
    // bodyContent.append("cargo_id", cargo_id);
    bodyContent.append("page", currentPage);
    bodyContent.append("limite", limit);
    bodyContent.append("status", status);
    // bodyContent.append("cpf", cpf);
    // bodyContent.append("vagaTipo", vagaTipo);


    const tableBody = document.querySelector('.table-hover tbody');
    tableBody.innerHTML = '';

    await fetch("/api/recursos", {
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
            totalItems = data[0]?.total_registros ?? 0;
            console.log(data)

            if (data && Array.isArray(data)) {
                data.forEach(recurso => {
                    console.log(recurso.nome)
                    const row = document.createElement('tr');
                    row.classList.add(
                        'candidate-row'
                    ); // Add class for potential future styling/selection

                    // Construct cells based on expected data structure from API
                    row.innerHTML = `
                        <td>${recurso.nome}</td>
                        <td>${recurso.inscricao_protocolo}</td>
                        <td>
                            <span class="badge ${ recurso.status == 'deferido' ? 'bg-success' : (recurso.status == 'indeferido' ? 'bg-danger' : 'bg-warning')}">
                                ${recurso.status}
                            </span>
                        </td>
                        <td>
                            <a href="/dashboard/recursos/detalhes/${recurso.recurso}" class="btn btn-sm btn-outline-primary" title="Detalhes">
                                <i class="fas fa-eye"></i> Detalhes
                            </a>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                // Update total count
                document.querySelector('.card-header .text-muted').textContent =
                    `Total: ${data.length} / ${totalItems} candidatos`;
            } else {
                // Handle case with no data
                tableBody.innerHTML =
                    '<tr><td colspan="4" class="text-center">Nenhum recurso encontrado.</td></tr>';
                document.querySelector('.card-header .text-muted').textContent = 'Total: 0 candidatos';
            }

            renderPagination();
        }
    );


}
</script>



<script>
let processo_id;
let status_insc;

document.addEventListener('DOMContentLoaded', function() {
    // Aplicar filtros e buscar inscrições da API
    document.getElementById('aplicarFiltros').addEventListener('click', function() {
        currentPage = 0;
        totalItems = 0;
        sessionStorage.clear();

        processo_id = document.getElementById('processoFilter').value;
        status_insc = document.getElementById('statusFilter').value;

        sessionStorage.setItem('processo_id', processo_id);
        sessionStorage.setItem('status', status_insc);

        carregar_recursos(processo_id, status_insc);
    });

    // Limpar filtros
    document.getElementById('limparFiltros').addEventListener('click', function() {
        sessionStorage.clear();
        currentPage = 0;

        document.getElementById('processoFilter').value = '';
        document.getElementById('statusFilter').value = 'todos';

        // Recarregar com filtros limpos
        carregar_recursos('', 'todos');
    });

    // Carregar filtros da sessão ou usar valores padrão
    processo_id = sessionStorage.getItem('processo_id') || '';
    status_insc = sessionStorage.getItem('status') || 'todos';
    currentPage = parseInt(sessionStorage.getItem('currentPage')) || 0;

    document.getElementById('processoFilter').value = processo_id;
    document.getElementById('statusFilter').value = status_insc;

    // Carregar os dados iniciais
    carregar_recursos(processo_id, status_insc);
});
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
            // carregar_recursos(1, 18);

            sessionStorage.setItem('currentPage', currentPage);
            carregar_recursos(processo_id, status_insc);
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
            carregar_recursos(processo_id, status_insc);
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
        carregar_recursos(processo_id, status_insc);
    });
    return li;
}
</script>
