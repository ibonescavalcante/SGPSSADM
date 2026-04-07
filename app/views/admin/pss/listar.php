<?php $this->layout("admin_template") ?>

<div class="admin-content">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="admin-title">Gerenciar PSS</h1>
            <p class="admin-subtitle">Gerencie todos os Processos Seletivos Simplificados</p>
        </div>
        <div class="flex gap-4">
            <a href="/admin/pss/criar-novo" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo PSS
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="filtro_titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" id="filtro_titulo" name="titulo" 
                           value="<?= htmlspecialchars($_GET['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Buscar por título..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div>
                    <label for="filtro_secretaria" class="block text-sm font-medium text-gray-700 mb-2">Secretaria</label>
                    <select id="filtro_secretaria" name="secretaria" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Todas as secretarias</option>
                        <option value="SEMAD" <?= ($_GET['secretaria'] ?? '') === 'SEMAD' ? 'selected' : '' ?>>SEMAD</option>
                        <option value="SEMED" <?= ($_GET['secretaria'] ?? '') === 'SEMED' ? 'selected' : '' ?>>SEMED</option>
                        <option value="SEMSA" <?= ($_GET['secretaria'] ?? '') === 'SEMSA' ? 'selected' : '' ?>>SEMSA</option>
                    </select>
                </div>
                
                <div>
                    <label for="filtro_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filtro_status" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Todos os status</option>
                        <option value="rascunho" <?= ($_GET['status'] ?? '') === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                        <option value="publicado" <?= ($_GET['status'] ?? '') === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                        <option value="inscricoes_abertas" <?= ($_GET['status'] ?? '') === 'inscricoes_abertas' ? 'selected' : '' ?>>Inscrições Abertas</option>
                        <option value="inscricoes_encerradas" <?= ($_GET['status'] ?? '') === 'inscricoes_encerradas' ? 'selected' : '' ?>>Inscrições Encerradas</option>
                        <option value="em_andamento" <?= ($_GET['status'] ?? '') === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="finalizado" <?= ($_GET['status'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        <option value="inativo" <?= ($_GET['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        <option value="cancelado" <?= ($_GET['status'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de PSS -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de PSS</h3>
            <div class="text-sm text-gray-500">
                <?= count($pss_list ?? []) ?> PSS encontrados
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pss_list ?? [])): ?>
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum PSS encontrado</h3>
                    <p class="text-gray-500 mb-6">Não há PSS cadastrados ou que atendam aos filtros selecionados.</p>
                    <a href="/admin/pss/criar-novo" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Primeiro PSS
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PSS
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Secretaria
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cargos
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Criado em
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pss_list as $pss): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <div class="text-sm font-semibold text-gray-900 mb-1">
                                                <?= htmlspecialchars($pss['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Ano: <?= htmlspecialchars($pss['ano_exercicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($pss['secretaria'])): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= htmlspecialchars($pss['secretaria'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                Não informado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'rascunho' => 'bg-gray-200 text-gray-700', // Cinza para rascunho
                                            'publicado' => 'bg-blue-100 text-blue-700', // Azul para publicado
                                            'inscricoes_abertas' => 'bg-green-100 text-green-700', // Verde para inscrições abertas
                                            'inscricoes_encerradas' => 'bg-yellow-100 text-yellow-700', // Amarelo para inscrições encerradas
                                            'em_andamento' => 'bg-orange-100 text-orange-700', // Laranja para em andamento
                                            'finalizado' => 'bg-purple-100 text-purple-700', // Roxo para finalizado
                                            'inativo' => 'bg-red-100 text-red-700', // Vermelho para inativo
                                            'cancelado' => 'bg-red-100 text-red-700' // Vermelho para cancelado
                                        ];
                                        $statusTexts = [
                                            'rascunho' => 'Rascunho',
                                            'publicado' => 'Publicado',
                                            'inscricoes_abertas' => 'Inscrições Abertas',
                                            'inscricoes_encerradas' => 'Inscrições Encerradas',
                                            'em_andamento' => 'Em Andamento',
                                            'finalizado' => 'Finalizado',
                                            'inativo' => 'Inativo',
                                            'cancelado' => 'Cancelado'
                                        ];
                                        $status = $pss['status_global'] ?? 'rascunho';
                                        $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        $statusText = $statusTexts[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-medium text-gray-900">
                                            <?= $pss['total_cargos'] ?? 0 ?>
                                        </span>
                                        <div class="text-xs text-gray-500">cargos</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        <?= date('d/m/Y', strtotime($pss['criado_em'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="/admin/pss/visualizar/<?= $pss['id'] ?>" 
                                               class="action-btn action-btn-view" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/pss/editar/<?= $pss['id'] ?>" 
                                               class="action-btn action-btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="inativarPss(<?= $pss['id'] ?>, '<?= htmlspecialchars($pss['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>')" 
                                                    class="action-btn action-btn-inactive" title="Inativar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if (($total_paginas ?? 1) > 1): ?>
                    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando <?= ($pagina_atual - 1) * $itens_por_pagina + 1 ?> a 
                                <?= min($pagina_atual * $itens_por_pagina, $total_itens) ?> de 
                                <?= $total_itens ?> resultados
                            </div>
                            <div class="flex gap-2">
                                <?php if ($pagina_atual > 1): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina_atual - 1])) ?>" 
                                       class="btn btn-outline btn-sm">
                                        <i class="fas fa-chevron-left"></i>
                                        Anterior
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" 
                                       class="btn <?= $i === $pagina_atual ? 'btn-primary' : 'btn-outline' ?> btn-sm">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($pagina_atual < $total_paginas): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina_atual + 1])) ?>" 
                                       class="btn btn-outline btn-sm">
                                        Próxima
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Inativação -->
<div id="modalInativar" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-orange-500 text-2xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Confirmar Inativação</h3>
            </div>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-500">
                Tem certeza que deseja inativar o PSS "<span id="nomePssInativar" class="font-semibold"></span>"?
                Esta ação pode ser revertida posteriormente.
            </p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="fecharModalInativar()" 
                    class="btn btn-outline">
                Cancelar
            </button>
            <button type="button" onclick="confirmarInativacao()" 
                    class="btn btn-warning">
                <i class="fas fa-ban"></i>
                Inativar
            </button>
        </div>
    </div>
</div>

<style>
    /* Grid System */
    .grid {
        display: grid;
    }
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    .gap-4 {
        gap: 1rem;
    }
    
    /* Layout Utilities */
    .w-full {
        width: 100%;
    }
    .flex {
        display: flex;
    }
    .flex-col {
        flex-direction: column;
    }
    .items-center {
        align-items: center;
    }
    .items-end {
        align-items: flex-end;
    }
    .justify-between {
        justify-content: space-between;
    }
    .justify-center {
        justify-content: center;
    }
    .justify-end {
        justify-content: flex-end;
    }
    .space-x-2 > :not([hidden]) ~ :not([hidden]) {
        margin-left: 0.5rem;
    }
    
    /* Spacing */
    .mb-1 {
        margin-bottom: 0.25rem;
    }
    .mb-2 {
        margin-bottom: 0.5rem;
    }
    .mb-4 {
        margin-bottom: 1rem;
    }
    .mb-6 {
        margin-bottom: 1.5rem;
    }
    .ml-3 {
        margin-left: 0.75rem;
    }
    .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .px-6 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    .py-3 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .py-12 {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }
    .p-6 {
        padding: 1.5rem;
    }
    .px-2\.5 {
        padding-left: 0.625rem;
        padding-right: 0.625rem;
    }
    .py-0\.5 {
        padding-top: 0.125rem;
        padding-bottom: 0.125rem;
    }
    
    /* Form Elements */
    .border {
        border-width: 1px;
    }
    .border-t {
        border-top-width: 1px;
    }
    .border-gray-200 {
        border-color: #e5e7eb;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
    .rounded-md {
        border-radius: 0.375rem;
    }
    .rounded-lg {
        border-radius: 0.5rem;
    }
    .rounded-full {
        border-radius: 9999px;
    }
    .focus\:outline-none:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--color-primary);
    }
    
    /* Table */
    .min-w-full {
        min-width: 100%;
    }
    .overflow-x-auto {
        overflow-x: auto;
    }
    .divide-y > :not([hidden]) ~ :not([hidden]) {
        border-top-width: 1px;
    }
    .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
        border-color: #e5e7eb;
    }
    .whitespace-nowrap {
        white-space: nowrap;
    }
    
    /* Text */
    .text-left {
        text-align: left;
    }
    .text-center {
        text-align: center;
    }
    .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
    .text-lg {
        font-size: 1.125rem;
        line-height: 1.75rem;
    }
    .text-2xl {
        font-size: 1.5rem;
        line-height: 2rem;
    }
    .text-6xl {
        font-size: 3.75rem;
        line-height: 1;
    }
    .font-medium {
        font-weight: 500;
    }
    .font-semibold {
        font-weight: 600;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    
    /* Colors */
    .text-gray-400 {
        color: #9ca3af;
    }
    .text-gray-500 {
        color: #6b7280;
    }
    .text-gray-700 {
        color: #374151;
    }
    .text-gray-800 {
        color: #1f2937;
    }
    .text-gray-900 {
        color: #111827;
    }
    .text-red-500 {
        color: #ef4444;
    }
    .bg-white {
        background-color: #ffffff;
    }
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    .bg-gray-100 {
        background-color: #f3f4f6;
    }
    .bg-gray-600 {
        background-color: #4b5563;
    }
    .bg-blue-100 {
        background-color: #dbeafe;
    }
    .text-blue-800 {
        color: #1e40af;
    }
    .bg-green-100 {
        background-color: #dcfce7;
    }
    .text-green-800 {
        color: #166534;
    }
    .bg-yellow-100 {
        background-color: #fef3c7;
    }
    .text-yellow-800 {
        color: #92400e;
    }
    .bg-purple-100 {
        background-color: #e9d5ff;
    }
    .text-purple-800 {
        color: #6b21a8;
    }
    .bg-red-100 {
        background-color: #fee2e2;
    }
    .bg-orange-100 {
        background-color: #fed7aa;
    }
    .text-orange-800 {
        color: #9a3412;
    }
    .bg-opacity-50 {
        background-opacity: 0.5;
    }
    
    /* Display */
    .inline-flex {
        display: inline-flex;
    }
    .hidden {
        display: none;
    }
    .block {
        display: block;
    }
    
    /* Position */
    .fixed {
        position: fixed;
    }
    .inset-0 {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    .flex-shrink-0 {
        flex-shrink: 0;
    }
    
    /* Z-index */
    .z-50 {
        z-index: 50;
    }
    
    /* Sizing */
    .max-w-md {
        max-width: 28rem;
    }
    .mx-4 {
        margin-left: 1rem;
        margin-right: 1rem;
    }
    
    /* Shadow */
    .shadow-xl {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Transitions */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    .duration-200 {
        transition-duration: 200ms;
    }
    
    /* Hover Effects */
    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }
    
    /* Action Buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 14px;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .action-btn-view {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .action-btn-view:hover {
        background-color: #bfdbfe;
        color: #1d4ed8;
    }
    
    .action-btn-edit {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .action-btn-edit:hover {
        background-color: #bbf7d0;
        color: #15803d;
    }
    
    .action-btn-duplicate {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .action-btn-duplicate:hover {
        background-color: #fde68a;
        color: #b45309;
    }
    
    .action-btn-report {
        background-color: #e9d5ff;
        color: #6b21a8;
    }
    
    .action-btn-report:hover {
        background-color: #ddd6fe;
        color: #7c2d12;
    }
    
    .action-btn-delete {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .action-btn-inactive {
        background-color: #fed7aa;
        color: #9a3412;
    }
    
    .action-btn-inactive:hover {
        background-color: #fdba74;
        color: #c2410c;
    }
    
    /* Button Warning */
    .btn-warning {
        background-color: #f59e0b;
        color: white;
        border: 1px solid #f59e0b;
    }
    
    .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
    }
    
    .text-orange-500 {
        color: #f97316;
    }
    .bg-gray-200 {
        background-color: #e5e7eb;
    }
    .text-gray-600 {
        color: #4b5563;
    }
    
    /* Button Danger */
    .btn-danger {
        background-color: #dc2626;
        color: white;
        border: 1px solid #dc2626;
    }
    
    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
    }
    
    /* Responsive */
    @media (min-width: 768px) {
        .md\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
</style>

<script>
let pssIdParaInativar = null;

function inativarPss(id, titulo) {
    pssIdParaInativar = id;
    document.getElementById('nomePssInativar').textContent = titulo;
    document.getElementById('modalInativar').classList.remove('hidden');
    document.getElementById('modalInativar').classList.add('flex');
}

function fecharModalInativar() {
    pssIdParaInativar = null;
            function confirmarInativacao() {
            const pssId = document.getElementById("inativarPssId").value;
            fetch(`/admin/pss/inativar/${pssId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro na requisição: " + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("PSS inativado com sucesso!");
                    window.location.reload();
                } else {
                    alert("Erro ao inativar PSS: " + (data.message || "Erro desconhecido"));
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Ocorreu um erro de comunicação com o servidor.");
            });
            fecharModalInativar();
        });
    }
}

// Fechar modal ao clicar fora dele
document.getElementById('modalInativar').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalInativar();
    }
});
</script>

