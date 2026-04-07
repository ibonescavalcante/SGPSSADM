<?php $this->layout("/painel/painel-template") ?>

<div x-data="{ 
    modalAberto: false, 
    inscricaoSelecionada: null, 
    processoNome: '',
    abrirModal(inscricaoId, nome) {
        this.inscricaoSelecionada = inscricaoId;
        this.processoNome = nome;
        this.modalAberto = true;
    },
    fecharModal() {
        this.modalAberto = false;
        this.inscricaoSelecionada = null;
        this.processoNome = '';
    },
    cancelarInscricao() {
        if (this.inscricaoSelecionada) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/painel/inscricao/cancelar/' + this.inscricaoSelecionada;
            document.body.appendChild(form);
            form.submit();
        }
    }
}" class="container mx-auto px-4 py-8">

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Minhas Inscrições</h1>
        <p class="text-gray-600 mb-8">Acompanhe o status das suas inscrições</p>

        <!-- Mensagens de sucesso/erro -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?= htmlspecialchars($_GET['success']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span><?= htmlspecialchars($_GET['erro']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($inscricoes)): ?>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                <i class="fas fa-info-circle text-blue-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">Nenhuma inscrição encontrada</h3>
                <p class="text-blue-600 mb-4">Você ainda não possui inscrições em processos seletivos.</p>
                <a href="/painel" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Voltar ao Painel
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($inscricoes as $inscricao): ?>
                    <?php
                    // Verificar se o prazo de inscrição ainda está aberto
                    $agora = new DateTime();
                    $fim_inscricao = new DateTime($inscricao['inscricao_fim'] ?? '1970-01-01');
                    $prazo_aberto = $agora <= $fim_inscricao;
                    
                    // FORÇAR DETECÇÃO DE CANCELAMENTO - Múltiplas verificações
                    $esta_cancelada = false;
                    
                    // 1. Verificar campo cancelada (múltiplos formatos)
                    if (isset($inscricao['cancelada'])) {
                        $cancelada_val = $inscricao['cancelada'];
                        if ($cancelada_val === true || $cancelada_val === 1 || $cancelada_val === '1' || 
                            $cancelada_val === 't' || $cancelada_val === 'true' || $cancelada_val === 'TRUE') {
                            $esta_cancelada = true;
                        }
                    }
                    
                    // 2. Verificar status (qualquer variação de "cancel")
                    $status_str = strtolower(trim($inscricao['status'] ?? ''));
                    if (strpos($status_str, 'cancel') !== false) {
                        $esta_cancelada = true;
                    }
                    
                    // 3. Verificar se texto contém "cancelad"
                    if (stripos($inscricao['status'] ?? '', 'cancelad') !== false) {
                        $esta_cancelada = true;
                    }
                    
                    // DEFINIR COR E TEXTO BASEADO NO CANCELAMENTO
                    if ($esta_cancelada) {
                        // FORÇAR COR VERMELHA
                        $status_display = [
                            'text' => 'Cancelada', 
                            'color' => 'bg-red-100 text-red-800'
                        ];
                    } else {
                        // Status normal
                        $status_info = [
                            'apta' => ['text' => 'Apta', 'color' => 'bg-green-100 text-green-800'],
                            'pendente' => ['text' => 'Pendente', 'color' => 'bg-yellow-100 text-yellow-800'],
                            'inapta' => ['text' => 'Inapta', 'color' => 'bg-red-100 text-red-800'],
                        ];
                        $status_display = $status_info[$status_str] ?? [
                            'text' => ucfirst($inscricao['status'] ?? 'Pendente'), 
                            'color' => 'bg-gray-100 text-gray-800'
                        ];
                    }
                    
                    $pode_cancelar = $prazo_aberto && !$esta_cancelada;
                    ?>
                    
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex-1 mb-4 lg:mb-0">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                        <?= htmlspecialchars($inscricao['pss_titulo']) ?>
                                    </h3>
                                    <p class="text-gray-600 mb-3">
                                        <?= htmlspecialchars($inscricao['cargo_nome']) ?>
                                    </p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                                        <div>
                                            <span class="font-medium">Inscrito em:</span>
                                            <?= date('d/m/Y', strtotime($inscricao['dt_inscricao'])) ?>
                                        </div>
                                        <div>
                                            <span class="font-medium">Protocolo:</span>
                                            <?= htmlspecialchars($inscricao['protocolo']) ?>
                                        </div>
                                        <?php if ($esta_cancelada && isset($inscricao['data_cancelamento']) && $inscricao['data_cancelamento']): ?>
                                            <div class="md:col-span-2">
                                                <span class="font-medium">Cancelada em:</span>
                                                <?= date('d/m/Y H:i', strtotime($inscricao['data_cancelamento'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col items-end space-y-3">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $status_display['color'] ?>">
                                        <?= $status_display['text'] ?>
                                    </span>
                                    
                                    <div class="flex flex-col space-y-2">
                                        <?php if (!$esta_cancelada): ?>
                                            <a href="/painel/inscricao/detalhes/<?= $inscricao['id'] ?>" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors text-center">
                                                Ver Detalhes
                                            </a>
                                            <a href="/inscricao/comprovante/<?= $inscricao['protocolo'] ?>" 
                                               target="_blank"
                                               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm transition-colors text-center">
                                                Comprovante
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($pode_cancelar): ?>
                                            <button @click="abrirModal(<?= $inscricao['id'] ?>, '<?= htmlspecialchars($inscricao['pss_titulo']) ?>')"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors">
                                                Cancelar Inscrição
                                            </button>
                                        <?php elseif (!$prazo_aberto && !$esta_cancelada): ?>
                                            <button disabled 
                                                    title="Não é possível cancelar após o prazo de inscrições"
                                                    class="bg-gray-300 text-gray-500 px-4 py-2 rounded text-sm cursor-not-allowed">
                                                Cancelar Inscrição
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Alertas sobre prazo -->
                            <?php if (!$prazo_aberto && !$esta_cancelada): ?>
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded p-3">
                                    <div class="flex items-center text-blue-700 text-sm">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <span>Prazo de inscrições encerrado. Não é mais possível cancelar ou editar esta inscrição.</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-8 text-center">
            <a href="/painel" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div x-show="modalAberto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @click.away="fecharModal()"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="fecharModal()"></div>
            
            <!-- Modal Content -->
            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg"
                 @click.stop>
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Confirmar Cancelamento</h3>
                    <button @click="fecharModal()" 
                            class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-700 mb-2">Tem certeza que deseja cancelar sua inscrição no processo:</p>
                    <p class="font-semibold text-gray-900" x-text="processoNome"></p>
                    
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-yellow-800 text-sm">
                                <strong>Atenção:</strong> Esta ação não pode ser desfeita. Após o cancelamento, você poderá se inscrever novamente apenas se o prazo de inscrições ainda estiver aberto.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button @click="fecharModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Não, manter inscrição
                    </button>
                    <button @click="cancelarInscricao()" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                        Sim, cancelar inscrição
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Garantir que o modal funcione corretamente */
[x-cloak] { display: none !important; }
</style>

