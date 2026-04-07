<?php
$this->layout("admin_template", [
    "title" => "Visualizar PSS - Administração",
    "current_page" => "gerenciar-pss"
]);
?>

<div class="admin-content">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="admin-title">Visualizar PSS</h1>
            <p class="admin-subtitle">Detalhes do Processo Seletivo Simplificado</p>
        </div>
        <div class="flex gap-4">
            <a href="/admin/pss" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="/admin/pss/editar/<?= $pss["id"] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar PSS
            </a>
        </div>
    </div>

    <!-- Informações do PSS -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Informações Gerais</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($pss["titulo"] ?? "", ENT_QUOTES, "UTF-8") ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ano de Exercício</label>
                    <p class="text-lg text-gray-900"><?= htmlspecialchars($pss["ano_exercicio"] ?? "", ENT_QUOTES, "UTF-8") ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secretaria</label>
                    <?php if (!empty($pss["secretaria"])): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($pss["secretaria"] ?? "", ENT_QUOTES, "UTF-8") ?>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-500">
                            Não informado
                        </span>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <?php
                    $statusColors = [
                        "rascunho" => "bg-gray-100 text-gray-800",
                        "publicado" => "bg-blue-100 text-blue-800",
                        "inscricoes_abertas" => "bg-green-100 text-green-800",
                        "inscricoes_encerradas" => "bg-yellow-100 text-yellow-800",
                        "em_andamento" => "bg-orange-100 text-orange-800",
                        "finalizado" => "bg-purple-100 text-purple-800",
                        "inativo" => "bg-gray-200 text-gray-600",
                        "cancelado" => "bg-red-100 text-red-800"
                    ];
                    $statusTexts = [
                        "rascunho" => "Rascunho",
                        "publicado" => "Publicado",
                        "inscricoes_abertas" => "Inscrições Abertas",
                        "inscricoes_encerradas" => "Inscrições Encerradas",
                        "em_andamento" => "Em Andamento",
                        "finalizado" => "Finalizado",
                        "inativo" => "Inativo",
                        "cancelado" => "Cancelado"
                    ];
                    $status = $pss["status_global"] ?? "rascunho";
                    $colorClass = $statusColors[$status] ?? "bg-gray-100 text-gray-800";
                    $statusText = $statusTexts[$status] ?? ucfirst(str_replace("_", " ", $status));
                    ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $colorClass ?>">
                        <?= $statusText ?>
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total de Cargos</label>
                    <p class="text-lg font-semibold text-gray-900"><?= $pss["total_cargos"] ?? 0 ?> cargos</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Criado em</label>
                    <p class="text-lg text-gray-900"><?= date("d/m/Y H:i", strtotime($pss["criado_em"])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrição -->
    <?php if (!empty($pss["descricao"])): ?>
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Descrição</h3>
        </div>
        <div class="card-body">
            <div class="prose max-w-none">
                <?= nl2br(htmlspecialchars($pss["descricao"] ?? "", ENT_QUOTES, "UTF-8")) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cargos Disponíveis -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Cargos Disponíveis</h3>
        </div>
        <div class="card-body">
            <?php if (empty($cargos)): ?>
                <div class="p-10 text-center">
                    <p class="text-gray-600">Nenhum cargo cadastrado para este PSS.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secretaria</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vagas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrições</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($cargos as $cargo): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($cargo["nome"]) ?></div>
                                        <div class="text-sm text-gray-500">Zona: <?= htmlspecialchars($cargo["zona"]) ?></div>
                                        <?php if (!empty($cargo["microrregiao"])): ?>
                                            <div class="text-sm text-gray-500">Microrregião: <?= htmlspecialchars($cargo["microrregiao"]) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= htmlspecialchars($cargo["secretaria"]) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= $cargo["vagas_total"] ?> Vagas
                                            </span>
                                            <?php if (isset($cargo["vagas_reserva"]) && $cargo["vagas_reserva"] > 0): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <?= $cargo["vagas_reserva"] ?> CR
                                                </span>
                                            <?php endif; ?>
                                            <?php if (isset($cargo["vagas_pcd"]) && $cargo["vagas_pcd"] > 0): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    <?= $cargo["vagas_pcd"] ?> PCD
                                                </span>
                                            <?php endif; ?>
                                            <?php if (isset($cargo["vagas_ppp"]) && $cargo["vagas_ppp"] > 0): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                    <?= $cargo["vagas_ppp"] ?> PPP
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $cargo["total_inscricoes"] ?? 0 ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/admin/pss/<?= $pss["id"] ?>/cargos/editar/<?= $cargo["id"] ?>" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ações -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ações Disponíveis</h3>
        </div>
        <div class="card-body">
            <div class="flex flex-wrap gap-4">
                <a href="/admin/pss/editar/<?= $pss["id"] ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Editar PSS
                </a>
                
                <a href="/admin/pss/<?= $pss["id"] ?>/cargos" class="btn btn-outline">
                    <i class="fas fa-users"></i>
                    Gerenciar Cargos
                </a>
                
                <a href="/admin/pss/<?= $pss["id"] ?>/etapas" class="btn btn-outline">
                    <i class="fas fa-list-ol"></i>
                    Gerenciar Etapas
                </a>
                
                <button onclick="inativarPss(<?= $pss["id"] ?>, '<?= htmlspecialchars($pss["titulo"] ?? "", ENT_QUOTES, "UTF-8") ?>')" 
                        class="btn btn-warning">
                    <i class="fas fa-ban"></i>
                    Inativar PSS
                </button>
            </div>
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
    .gap-6 {
        gap: 1.5rem;
    }
    
    /* Layout Utilities */
    .flex {
        display: flex;
    }
    .flex-wrap {
        flex-wrap: wrap;
    }
    .items-center {
        align-items: center;
    }
    .justify-between {
        justify-content: space-between;
    }
    .justify-end {
        justify-content: flex-end;
    }
    
    /* Spacing */
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
    .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    .p-6 {
        padding: 1.5rem;
    }
    .mx-4 {
        margin-left: 1rem;
        margin-right: 1rem;
    }
    
    /* Text */
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
    .font-medium {
        font-weight: 500;
    }
    .font-semibold {
        font-weight: 600;
    }
    
    /* Colors */
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
    .text-orange-500 {
        color: #f97316;
    }
    .bg-white {
        background-color: #ffffff;
    }
    .bg-gray-600 {
        background-color: #4b5563;
    }
    .bg-gray-100 {
        background-color: #f3f4f6;
    }
    .bg-gray-200 {
        background-color: #e5e7eb;
    }
    .text-gray-600 {
        color: #4b5563;
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
    .bg-orange-100 {
        background-color: #fed7aa;
    }
    .text-orange-800 {
        color: #9a3412;
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
    .text-red-800 {
        color: #991b1b;
    }
    .bg-opacity-50 {
        background-opacity: 0.5;
    }
    
    /* Display */
    .block {
        display: block;
    }
    .inline-flex {
        display: inline-flex;
    }
    .hidden {
        display: none;
    }
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
    
    /* Border */
    .rounded-lg {
        border-radius: 0.5rem;
    }
    .rounded-full {
        border-radius: 9999px;
    }
    
    /* Z-index */
    .z-50 {
        z-index: 50;
    }
    
    /* Sizing */
    .max-w-md {
        max-width: 28rem;
    }
    .max-w-none {
        max-width: none;
    }
    .w-full {
        width: 100%;
    }
    
    /* Shadow */
    .shadow-xl {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Prose */
    .prose {
        color: #374151;
        line-height: 1.75;
    }
    
    /* Responsive */
    @media (min-width: 768px) {
        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<script>
let pssIdParaInativar = null;

function inativarPss(id, titulo) {
    pssIdParaInativar = id;
    document.getElementById("nomePssInativar").textContent = titulo;
    document.getElementById("modalInativar").classList.remove("hidden");
    document.getElementById("modalInativar").classList.add("flex");
}

function fecharModalInativar() {
    pssIdParaInativar = null;
    document.getElementById("modalInativar").classList.add("hidden");
    document.getElementById("modalInativar").classList.remove("flex");
}

function confirmarInativacao() {
    if (pssIdParaInativar) {
        fetch(`/admin/pss/inativar/${pssIdParaInativar}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("PSS inativado com sucesso!");
                window.location.href = "/admin/pss";
            } else {
                alert("Erro ao inativar PSS: " + (data.erro || "Erro desconhecido"));
            }
        })
        .catch(error => {
            console.error("Erro:", error);
            alert("Erro ao inativar PSS");
        })
        .finally(() => {
            fecharModalInativar();
        });
    }
}

// Fechar modal ao clicar fora dele
document.getElementById("modalInativar").addEventListener("click", function(e) {
    if (e.target === this) {
        fecharModalInativar();
    }
});
</script>

