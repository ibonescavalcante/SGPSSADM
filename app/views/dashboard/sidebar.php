<!-- Sidebar -->
<div class="col-lg-2 col-md-3 p-0 sidebar d-none d-md-block border">
    <div class="p-3">
        <h5 class="text-uppercase text-muted small fw-bold">Navegação</h5>
    </div>
    <ul class="nav flex-column" id="sidebarNav">
        <li class="nav-item">
            <a class="nav-link active" href="/dashboard">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/processos" disabled>
                <i class="fas fa-file-alt me-2"></i> Processos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/inscricoes">
                <i class="fas fa-clipboard-list me-2"></i> Inscrições
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/recursos">
                <i class="fas fa-file-alt me-2"></i> Recursos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/relatorios" disabled>
                <i class="fas fa-chart-bar me-2"></i> Relatórios
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/configuracoes" disabled>
                <i class="fas fa-cog me-2"></i> Configurações
            </a>
        </li>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('#sidebarNav .nav-link');
        const path = window.location.pathname;

        links.forEach(link => {
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>