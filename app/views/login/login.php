<?php $this->layout('template') ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Logo e título -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Acesso ao Sistema</h2>
                <p class="text-gray-600">Para prosseguir, entre com seu CPF</p>
            </div>

            <!-- Mensagem de erro -->
            <?php if (isset($erro)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800"><?= htmlspecialchars($erro) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                        CPF
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="cpf" 
                            name="cpf" 
                            maxlength="14" 
                            pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}"
                            placeholder="000.000.000-00"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-lg"
                            oninput="mascaraCPF(this)" 
                            required
                            autocomplete="username"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out transform hover:scale-105"
                        style="background-color: var(--color-primary); hover:background-color: var(--color-primary-dark);"
                        onmouseover="this.style.backgroundColor='var(--color-primary-dark)'"
                        onmouseout="this.style.backgroundColor='var(--color-primary)'"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-green-300 group-hover:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        Continuar
                    </button>
                </div>
            </form>

            <!-- Links adicionais -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Primeira vez no sistema? 
                    <span class="font-medium text-green-600">Cadastre-se inserindo seu CPF acima</span>
                </p>
            </div>
        </div>

        <!-- Informações adicionais -->
        <div class="text-center">
            <p class="text-sm text-white opacity-90">
                Sistema de Processo Seletivo Simplificado
            </p>
            <p class="text-xs text-white opacity-75 mt-1">
                Prefeitura Municipal de Parauapebas
            </p>
        </div>
    </div>
</div>

<style>
/* Animação suave para o formulário */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.max-w-md {
    animation: fadeInUp 0.6s ease-out;
}

/* Efeito de foco melhorado */
input:focus {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Responsividade melhorada */
@media (max-width: 640px) {
    .max-w-md {
        margin: 0 16px;
    }
    
    .bg-white {
        padding: 24px 20px;
    }
}
</style>

