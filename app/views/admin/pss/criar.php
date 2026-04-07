<?php $this->layout("admin_template") ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Cabeçalho -->
        <div class="bg-white shadow-md rounded-lg mb-6">
            <div class="bg-green-800 text-white p-6 rounded-t-lg">
                <h1 class="text-2xl font-bold">Criar Novo PSS</h1>
                <p class="text-green-100">Configure todos os parâmetros do processo seletivo</p>
            </div>
        </div>

        <?php if (isset($erro)): ?>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <p class="text-red-700"><?= htmlspecialchars($erro) ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form method="POST" class="space-y-8">
            
            <!-- Informações Básicas -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informações Básicas</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-sm font-medium text-gray-700">Título do PSS *</label>
                        <input type="text" id="titulo" name="titulo" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                               placeholder="Ex: PSS Prefeitura Municipal de Parauapebas - Edital 001/2025">
                    </div>
                    
                    <div>
                        <label for="secretaria" class="block text-sm font-medium text-gray-700">Secretaria *</label>
                        <input type="text" id="secretaria" name="secretaria" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                               placeholder="Ex: Secretaria Municipal de Saúde">
                    </div>
                    
                    <div>
                        <label for="ano_exercicio" class="block text-sm font-medium text-gray-700">Ano de Exercício *</label>
                        <input type="number" id="ano_exercicio" name="ano_exercicio" required min="2024" max="2030" value="2025"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                  placeholder="Descrição detalhada do processo seletivo..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Prazos -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Prazos do Processo</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="inscricao_ini" class="block text-sm font-medium text-gray-700">Início das Inscrições *</label>
                        <input type="datetime-local" id="inscricao_ini" name="inscricao_ini" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="inscricao_fim" class="block text-sm font-medium text-gray-700">Fim das Inscrições *</label>
                        <input type="datetime-local" id="inscricao_fim" name="inscricao_fim" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="analise_ini" class="block text-sm font-medium text-gray-700">Início Análise de Documentos</label>
                        <input type="datetime-local" id="analise_ini" name="analise_ini"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="analise_fim" class="block text-sm font-medium text-gray-700">Fim Análise de Documentos</label>
                        <input type="datetime-local" id="analise_fim" name="analise_fim"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="recursos_ini" class="block text-sm font-medium text-gray-700">Início Período de Recursos</label>
                        <input type="datetime-local" id="recursos_ini" name="recursos_ini"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="recursos_fim" class="block text-sm font-medium text-gray-700">Fim Período de Recursos</label>
                        <input type="datetime-local" id="recursos_fim" name="recursos_fim"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="resultado_final" class="block text-sm font-medium text-gray-700">Data do Resultado Final</label>
                        <input type="datetime-local" id="resultado_final" name="resultado_final"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Configurações do Processo</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="permite_nome_social" name="permite_nome_social" checked
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="permite_nome_social" class="ml-2 block text-sm text-gray-900">
                            Permitir nome social (será exibido antes do nome civil, separado por hífen)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="exige_comprovante_residencia" name="exige_comprovante_residencia" checked
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="exige_comprovante_residencia" class="ml-2 block text-sm text-gray-900">
                            Exigir comprovante de residência
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="permite_recursos" name="permite_recursos" checked
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="permite_recursos" class="ml-2 block text-sm text-gray-900">
                            Permitir recursos (candidatos poderão contestar resultados)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="permite_impugnacao" name="permite_impugnacao" checked
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="permite_impugnacao" class="ml-2 block text-sm text-gray-900">
                            Permitir impugnação do edital
                        </label>
                    </div>
                </div>
            </div>

            <!-- Documentos Obrigatórios -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Documentos Obrigatórios</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="doc_identidade" name="documentos_obrigatorios[]" value="documento_identidade" checked disabled
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="doc_identidade" class="ml-2 block text-sm text-gray-900">
                            Documento de Identidade (obrigatório)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="doc_cpf" name="documentos_obrigatorios[]" value="cpf" checked disabled
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="doc_cpf" class="ml-2 block text-sm text-gray-900">
                            CPF (obrigatório)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="doc_escolaridade" name="documentos_obrigatorios[]" value="comprovante_escolaridade" checked disabled
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="doc_escolaridade" class="ml-2 block text-sm text-gray-900">
                            Comprovante de Escolaridade (obrigatório)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Status do Processo</h2>
                
                <div>
                    <label for="status_global" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select id="status_global" name="status_global" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="em_andamento">Em Andamento</option>
                        <option value="suspenso">Suspenso</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-between items-center pt-6">
                <a href="/admin/pss" 
                   class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="inline-block cursor-pointer align-middle rounded-[3px] border border-green-600 bg-green-600 px-8 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                    Criar PSS
                </button>
            </div>
        </form>
    </div>
</div>

