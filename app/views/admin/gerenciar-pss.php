<?php $this->layout("admin_template", ["title" => "Gerenciar PSS", "breadcrumb" => ["Administração", "PSS", "Gerenciar PSS"]]) ?>

<style>
/* Estilos específicos para a página Gerenciar PSS */
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
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: url("data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grid\" width=\"10\" height=\"10\" patternUnits=\"userSpaceOnUse\"><path d=\"M 10 0 L 0 0 0 10\" fill=\"none\" stroke=\"rgba(255,255,255,0.1 )\" stroke-width=\"0.5\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grid)\"/></svg>");
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

/* Container Principal */
.content-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
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
    content: "";
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

/* Tabela */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1.5rem;
}

.table th,
.table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--color-border);
}

.table th {
    background-color: var(--color-gray-100);
    font-weight: 600;
    color: var(--color-text);
}

.table tbody tr:hover {
    background-color: var(--color-gray-50);
}

.status-badge {
    padding: 0.25em 0.6em;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.375rem;
}
.status-em_andamento {
    background-color: #ffc107;
    color: #000;
}
.status-finalizado {
    background-color: #28a745;
}
.status-cancelado {
    background-color: #dc3545;
}
</style>

<!-- Header da Página -->
<div class="page-header">
    <div class="header-content">
        <div class="header-info">
            <div class="header-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="header-text">
                <h1>Gerenciar PSS</h1>
                <p>Visualize, edite e exclua Processos Seletivos Simplificados existentes</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/admin/pss/criar-novo" class="btn btn-primary"><i class="fas fa-plus"></i> Criar Novo PSS</a>
        </div>
    </div>
</div>

<div class="content-container">
    <main class="main-content">
        <div class="form-section">
            <div class="section-header">
                <h2 class="section-title">Processos Seletivos</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Ano</th>
                        <th>Secretaria</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pss_list)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum PSS encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pss_list as $pss): ?>
                            <tr>
                                <td><?= htmlspecialchars($pss["titulo"]) ?></td>
                                <td><?= htmlspecialchars($pss["ano_exercicio"]) ?></td>
                                <td><?= htmlspecialchars($pss["secretaria"]) ?></td>
                                <td><span class="status-badge status-<?= htmlspecialchars($pss["status_global"]) ?>"><?= ucfirst(str_replace("_", " ", $pss["status_global"])) ?></span></td>
                                <td>
                                    <a href="/admin/pss/editar/<?= $pss["id"] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="/admin/pss/excluir/<?= $pss["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm("Tem certeza que deseja excluir este PSS?")"><i class="fas fa-trash"></i> Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
