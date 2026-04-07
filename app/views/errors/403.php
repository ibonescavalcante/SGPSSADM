<?php $this->layout("/painel/painel-template") ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-red-50 border border-red-200 rounded-lg p-8">
            <div class="flex justify-center mb-6">
                <svg class="w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-red-800 mb-4">Acesso Negado</h1>
            
            <p class="text-red-700 mb-6 text-lg">
                <?= htmlspecialchars($message ?? 'Você não tem permissão para acessar este recurso.') ?>
            </p>
            
            <div class="bg-red-100 border border-red-300 rounded p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-red-800 text-sm">
                        <strong>Motivos possíveis:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            <li>Você está tentando acessar dados que não pertencem a você</li>
                            <li>Sua sessão pode ter expirado</li>
                            <li>Você não tem as permissões necessárias</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/painel" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Voltar ao Painel
                </a>
                
                <a href="/logout" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Fazer Logout
                </a>
            </div>
            
            <p class="text-gray-600 text-sm mt-6">
                Se você acredita que isso é um erro, entre em contato com o suporte.
            </p>
        </div>
    </div>
</div>

