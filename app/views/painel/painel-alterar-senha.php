<?php $this->layout('/painel/painel-template') ?>

<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg">
        <!-- Header -->
        <div class="bg-green-900 text-white p-6 rounded-t-lg">
            <h1 class="text-2xl font-bold">ALTERAR SENHA</h1>
            <p class="text-green-200">Mantenha sua conta segura</p>
        </div>

        <!-- Mensagens de Erro/Sucesso -->
        <?php if (isset($erro) && $erro): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-none" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Erro!</p>
                    <p class="text-sm"><?= htmlspecialchars($erro) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulário -->
        <div class="p-8">
            <form method="POST" action="/painel/alterar-senha" id="alterarSenhaForm" novalidate>
                <div class="space-y-6">
                    <!-- Senha Atual -->
                    <div>
                        <label for="senha_atual" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-gray-500"></i>
                            Senha Atual *
                        </label>
                        <input type="password" 
                               id="senha_atual" 
                               name="senha_atual" 
                               required
                               autocomplete="current-password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                               placeholder="Digite sua senha atual">
                        <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-senha_atual"></div>
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <label for="nova_senha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-2 text-gray-500"></i>
                            Nova Senha *
                        </label>
                        <input type="password" 
                               id="nova_senha" 
                               name="nova_senha" 
                               required
                               autocomplete="new-password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                               placeholder="Digite sua nova senha">
                        <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-nova_senha"></div>
                        
                        <!-- Indicador de força da senha -->
                        <div class="mt-2">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <span id="password-strength-text" class="text-xs text-gray-500"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmar Nova Senha -->
                    <div>
                        <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-double mr-2 text-gray-500"></i>
                            Confirmar Nova Senha *
                        </label>
                        <input type="password" 
                               id="confirmar_senha" 
                               name="confirmar_senha" 
                               required
                               autocomplete="new-password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                               placeholder="Confirme sua nova senha">
                        <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-confirmar_senha"></div>
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button type="button" 
                                onclick="window.location.href='/painel'"
                                class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        
                        <button type="submit" 
                                id="submitBtn"
                                class="flex-1 px-6 py-3 bg-green-900 text-white rounded-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-save mr-2"></i>
                            <span id="submitText">Alterar Senha</span>
                            <i class="fas fa-spinner fa-spin ml-2 hidden" id="submitSpinner"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Dicas de Segurança -->
        <div class="bg-blue-50 border-t border-blue-200 p-6 rounded-b-lg">
            <h3 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-shield-alt mr-2"></i>
                Dicas de Segurança
            </h3>
            <ul class="text-xs text-blue-700 space-y-1">
                <li>• Use uma combinação de letras maiúsculas, minúsculas, números e símbolos</li>
                <li>• Evite usar informações pessoais como nome, data de nascimento ou CPF</li>
                <li>• Não compartilhe sua senha com outras pessoas</li>
                <li>• Altere sua senha regularmente</li>
            </ul>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('alterarSenhaForm');
    const senhaAtual = document.getElementById('senha_atual');
    const novaSenha = document.getElementById('nova_senha');
    const confirmarSenha = document.getElementById('confirmar_senha');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    // Função para validar força da senha
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = '';
        
        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
        const texts = ['Muito fraca', 'Fraca', 'Média', 'Forte'];
        const widths = [25, 50, 75, 100];
        
        if (strength <= 2) {
            strengthBar.style.backgroundColor = colors[0];
            strengthBar.style.width = widths[0] + '%';
            strengthText.textContent = texts[0];
        } else if (strength <= 3) {
            strengthBar.style.backgroundColor = colors[1];
            strengthBar.style.width = widths[1] + '%';
            strengthText.textContent = texts[1];
        } else if (strength <= 4) {
            strengthBar.style.backgroundColor = colors[2];
            strengthBar.style.width = widths[2] + '%';
            strengthText.textContent = texts[2];
        } else {
            strengthBar.style.backgroundColor = colors[3];
            strengthBar.style.width = widths[3] + '%';
            strengthText.textContent = texts[3];
        }
        
        return strength;
    }

    // Função para mostrar erro
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById('error-' + fieldId);
        
        field.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        field.classList.remove('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
        
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }

    // Função para limpar erro
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById('error-' + fieldId);
        
        field.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        field.classList.add('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
        
        errorDiv.classList.add('hidden');
    }

    // Validação em tempo real da nova senha
    novaSenha.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length > 0) {
            checkPasswordStrength(password);
            
            if (password.length < 6) {
                showError('nova_senha', 'A senha deve ter pelo menos 6 caracteres');
            } else {
                clearError('nova_senha');
            }
        } else {
            strengthBar.style.width = '0%';
            strengthText.textContent = '';
            clearError('nova_senha');
        }
        
        // Revalidar confirmação se já foi preenchida
        if (confirmarSenha.value.length > 0) {
            validatePasswordMatch();
        }
    });

    // Validação da confirmação de senha
    function validatePasswordMatch() {
        if (confirmarSenha.value !== novaSenha.value) {
            showError('confirmar_senha', 'As senhas não coincidem');
            return false;
        } else {
            clearError('confirmar_senha');
            return true;
        }
    }

    confirmarSenha.addEventListener('input', validatePasswordMatch);

    // Limpar erros quando o usuário começar a digitar
    senhaAtual.addEventListener('input', function() {
        if (this.value.length > 0) {
            clearError('senha_atual');
        }
    });

    // Validação do formulário
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Validar senha atual
        if (!senhaAtual.value.trim()) {
            showError('senha_atual', 'A senha atual é obrigatória');
            isValid = false;
        } else {
            clearError('senha_atual');
        }
        
        // Validar nova senha
        if (!novaSenha.value.trim()) {
            showError('nova_senha', 'A nova senha é obrigatória');
            isValid = false;
        } else if (novaSenha.value.length < 6) {
            showError('nova_senha', 'A nova senha deve ter pelo menos 6 caracteres');
            isValid = false;
        } else {
            clearError('nova_senha');
        }
        
        // Validar confirmação
        if (!confirmarSenha.value.trim()) {
            showError('confirmar_senha', 'A confirmação da senha é obrigatória');
            isValid = false;
        } else if (!validatePasswordMatch()) {
            isValid = false;
        }
        
        // Verificar se a nova senha é diferente da atual
        if (senhaAtual.value === novaSenha.value) {
            showError('nova_senha', 'A nova senha deve ser diferente da senha atual');
            isValid = false;
        }
        
        if (isValid) {
            // Mostrar loading
            submitBtn.disabled = true;
            submitText.textContent = 'Alterando...';
            submitSpinner.classList.remove('hidden');
            
            // Submeter formulário
            this.submit();
        }
    });

    // Mostrar/ocultar senha
    document.querySelectorAll('input[type="password"]').forEach(function(input) {
        const container = input.parentElement;
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        
        container.style.position = 'relative';
        container.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    });
});
</script>

