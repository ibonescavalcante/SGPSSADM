<?php
$this->layout("admin_template", [
    "title" => "Editar PSS - Administração",
    "current_page" => "gerenciar-pss"
]);
?>

<div class="admin-content">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="admin-title">Editar PSS</h1>
            <p class="admin-subtitle">Edite as informações do Processo Seletivo Simplificado</p>
        </div>
        <div class="flex gap-4">
            <a href="/admin/pss" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="/admin/pss/visualizar/<?= $pss['id'] ?>" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
        </div>
    </div>

    <!-- Formulário de Edição -->
    <form method="POST" action="/admin/pss/atualizar/<?= $pss['id'] ?>" class="space-y-6">
        <!-- Informações Básicas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informações Básicas</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título do PSS <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo" name="titulo" required
                               value="<?= htmlspecialchars($pss['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Ex: PSS Prefeitura Municipal de Parauapebas - Edital 001/2025">
                    </div>
                    
                    <div>
                        <label for="ano_exercicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Ano de Exercício <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="ano_exercicio" name="ano_exercicio" required
                               value="<?= htmlspecialchars($pss['ano_exercicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               min="2020" max="2030"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label for="secretaria" class="block text-sm font-medium text-gray-700 mb-2">
                            Secretaria
                        </label>
                        <select id="secretaria" name="secretaria"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Selecione uma secretaria</option>
                            <?php if (!empty($secretarias)): ?>
                                <?php foreach ($secretarias as $secretaria): ?>
                                    <option value="<?= htmlspecialchars($secretaria['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                            <?= ($pss['secretaria'] ?? '') === $secretaria['nome'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($secretaria['nome'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="SEMAD" <?= ($pss['secretaria'] ?? '') === 'SEMAD' ? 'selected' : '' ?>>SEMAD</option>
                                <option value="SEMED" <?= ($pss['secretaria'] ?? '') === 'SEMED' ? 'selected' : '' ?>>SEMED</option>
                                <option value="SEMSA" <?= ($pss['secretaria'] ?? '') === 'SEMSA' ? 'selected' : '' ?>>SEMSA</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status_global" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status_global" name="status_global" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="rascunho" <?= ($pss['status_global'] ?? '') === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                            <option value="publicado" <?= ($pss['status_global'] ?? '') === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                            <option value="inscricoes_abertas" <?= ($pss['status_global'] ?? '') === 'inscricoes_abertas' ? 'selected' : '' ?>>Inscrições Abertas</option>
                            <option value="inscricoes_encerradas" <?= ($pss['status_global'] ?? '') === 'inscricoes_encerradas' ? 'selected' : '' ?>>Inscrições Encerradas</option>
                            <option value="em_andamento" <?= ($pss['status_global'] ?? '') === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
                            <option value="finalizado" <?= ($pss['status_global'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            <option value="inativo" <?= ($pss['status_global'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                            <option value="cancelado" <?= ($pss['status_global'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição
                        </label>
                        <textarea id="descricao" name="descricao" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Descrição detalhada do PSS..."><?= htmlspecialchars($pss['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end gap-4">
            <a href="/admin/pss" class="btn btn-outline">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Salvar Alterações
            </button>
        </div>
    </form>
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
    .space-y-6 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 1.5rem;
    }
    .mb-2 {
        margin-bottom: 0.5rem;
    }
    .mb-6 {
        margin-bottom: 1.5rem;
    }
    .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    
    /* Form Elements */
    .w-full {
        width: 100%;
    }
    .border {
        border-width: 1px;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
    .rounded-md {
        border-radius: 0.375rem;
    }
    .focus\:outline-none:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--color-primary);
    }
    
    /* Text */
    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
    .font-medium {
        font-weight: 500;
    }
    
    /* Colors */
    .text-gray-700 {
        color: #374151;
    }
    .text-red-500 {
        color: #ef4444;
    }
    
    /* Display */
    .block {
        display: block;
    }
    
    /* Responsive */
    @media (min-width: 768px) {
        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .md\:col-span-2 {
            grid-column: span 2 / span 2;
        }
    }
</style>

