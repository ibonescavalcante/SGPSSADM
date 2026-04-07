<?php $this->layout('admin_template') ?>

<div class="page-header">
    <div class="header-content">
        <h1 class="page-title">Alterar Senha</h1>
        <p class="page-subtitle">Mantenha sua conta segura com uma senha forte</p>
    </div>
    <a href="/admin/perfil" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>
        Voltar ao Perfil
    </a>
</div>

<div class="password-change-container">
    <div class="password-form-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-key"></i>
                Alterar Senha
            </h3>
        </div>
        <div class="card-body">
            <form id="passwordForm" method="POST" action="/admin/alterar-senha">
                <?php if (isset($mensagem)): ?>
                    <div class="alert alert-<?= $tipo_mensagem === 'sucesso' ? 'success' : 'error' ?>">
                        <i class="fas fa-<?= $tipo_mensagem === 'sucesso' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="senha_atual">Senha Atual</label>
                    <div class="password-input">
                        <input type="password" id="senha_atual" name="senha_atual" 
                               class="form-control" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('senha_atual')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nova_senha">Nova Senha</label>
                    <div class="password-input">
                        <input type="password" id="nova_senha" name="nova_senha" 
                               class="form-control" required minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('nova_senha')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Digite uma senha</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <div class="password-input">
                        <input type="password" id="confirmar_senha" name="confirmar_senha" 
                               class="form-control" required minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmar_senha')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>

                <div class="password-requirements">
                    <h4>Requisitos da senha:</h4>
                    <ul class="requirements-list">
                        <li id="req-length">
                            <i class="fas fa-times"></i>
                            Pelo menos 8 caracteres
                        </li>
                        <li id="req-uppercase">
                            <i class="fas fa-times"></i>
                            Uma letra maiúscula
                        </li>
                        <li id="req-lowercase">
                            <i class="fas fa-times"></i>
                            Uma letra minúscula
                        </li>
                        <li id="req-number">
                            <i class="fas fa-times"></i>
                            Um número
                        </li>
                        <li id="req-special">
                            <i class="fas fa-times"></i>
                            Um caractere especial (!@#$%^&*)
                        </li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save"></i>
                        Alterar Senha
                    </button>
                    <a href="/admin/perfil" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="security-tips-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shield-alt"></i>
                Dicas de Segurança
            </h3>
        </div>
        <div class="card-body">
            <div class="tips-list">
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="tip-content">
                        <h4>Use uma senha forte</h4>
                        <p>Combine letras maiúsculas e minúsculas, números e símbolos</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div class="tip-content">
                        <h4>Mantenha em segredo</h4>
                        <p>Nunca compartilhe sua senha com outras pessoas</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="tip-content">
                        <h4>Altere regularmente</h4>
                        <p>Recomendamos alterar a senha a cada 3-6 meses</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="tip-content">
                        <h4>Evite informações pessoais</h4>
                        <p>Não use datas de nascimento, nomes ou informações óbvias</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    let strength = 0;
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById(`req-${req}`);
        const icon = element.querySelector('i');
        
        if (requirements[req]) {
            element.classList.add('valid');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-check');
            strength++;
        } else {
            element.classList.remove('valid');
            icon.classList.remove('fa-check');
            icon.classList.add('fa-times');
        }
    });
    
    // Update strength bar
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    const percentage = (strength / 5) * 100;
    
    strengthBar.style.width = percentage + '%';
    
    if (strength === 0) {
        strengthBar.className = 'strength-fill';
        strengthText.textContent = 'Digite uma senha';
    } else if (strength <= 2) {
        strengthBar.className = 'strength-fill weak';
        strengthText.textContent = 'Senha fraca';
    } else if (strength <= 3) {
        strengthBar.className = 'strength-fill medium';
        strengthText.textContent = 'Senha média';
    } else if (strength <= 4) {
        strengthBar.className = 'strength-fill good';
        strengthText.textContent = 'Senha boa';
    } else {
        strengthBar.className = 'strength-fill strong';
        strengthText.textContent = 'Senha forte';
    }
    
    return strength === 5;
}

function checkPasswordMatch() {
    const newPassword = document.getElementById('nova_senha').value;
    const confirmPassword = document.getElementById('confirmar_senha').value;
    const matchIndicator = document.getElementById('passwordMatch');
    
    if (confirmPassword === '') {
        matchIndicator.innerHTML = '';
        return false;
    }
    
    if (newPassword === confirmPassword) {
        matchIndicator.innerHTML = '<i class="fas fa-check"></i> Senhas coincidem';
        matchIndicator.className = 'password-match valid';
        return true;
    } else {
        matchIndicator.innerHTML = '<i class="fas fa-times"></i> Senhas não coincidem';
        matchIndicator.className = 'password-match invalid';
        return false;
    }
}

function updateSubmitButton() {
    const currentPassword = document.getElementById('senha_atual').value;
    const newPassword = document.getElementById('nova_senha').value;
    const confirmPassword = document.getElementById('confirmar_senha').value;
    const submitBtn = document.getElementById('submitBtn');
    
    const isStrong = checkPasswordStrength(newPassword);
    const isMatching = checkPasswordMatch();
    const hasCurrentPassword = currentPassword.length > 0;
    
    if (hasCurrentPassword && isStrong && isMatching) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('disabled');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add('disabled');
    }
}

// Event listeners
document.getElementById('nova_senha').addEventListener('input', updateSubmitButton);
document.getElementById('confirmar_senha').addEventListener('input', updateSubmitButton);
document.getElementById('senha_atual').addEventListener('input', updateSubmitButton);

// Form submission
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('nova_senha').value;
    const confirmPassword = document.getElementById('confirmar_senha').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        return false;
    }
    
    if (!checkPasswordStrength(newPassword)) {
        e.preventDefault();
        alert('A senha não atende aos requisitos de segurança!');
        return false;
    }
});
</script>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--color-border);
}

.page-title {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-primary);
}

.page-subtitle {
    margin: 0;
    color: var(--color-text-light);
    font-size: 1rem;
}

.password-change-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    max-width: 1200px;
}

.password-form-card,
.security-tips-card {
    background: var(--color-white);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--color-text);
    font-size: 0.875rem;
}

.password-input {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input .form-control {
    padding-right: 3rem;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    color: var(--color-text-light);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: color var(--transition-fast);
}

.password-toggle:hover {
    color: var(--color-primary);
}

.password-strength {
    margin-top: 0.75rem;
}

.strength-bar {
    width: 100%;
    height: 6px;
    background: var(--color-gray-200);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all var(--transition-normal);
    border-radius: 3px;
}

.strength-fill.weak {
    background: var(--color-error);
}

.strength-fill.medium {
    background: var(--color-warning);
}

.strength-fill.good {
    background: #FFC107;
}

.strength-fill.strong {
    background: var(--color-success);
}

.strength-text {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--color-text-light);
}

.password-match {
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.password-match.valid {
    color: var(--color-success);
}

.password-match.invalid {
    color: var(--color-error);
}

.password-requirements {
    background: var(--color-gray-50);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.password-requirements h4 {
    margin: 0 0 1rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text);
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.requirements-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.8125rem;
    color: var(--color-text-light);
    transition: color var(--transition-fast);
}

.requirements-list li.valid {
    color: var(--color-success);
}

.requirements-list li i {
    width: 16px;
    text-align: center;
    font-size: 0.75rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--color-border);
}

.btn.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.tip-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.tip-content h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text);
}

.tip-content p {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--color-text-light);
    line-height: 1.4;
}

.alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: rgba(76, 175, 80, 0.1);
    color: var(--color-success);
    border: 1px solid rgba(76, 175, 80, 0.2);
}

.alert-error {
    background: rgba(244, 67, 54, 0.1);
    color: var(--color-error);
    border: 1px solid rgba(244, 67, 54, 0.2);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
        text-align: center;
    }

    .password-change-container {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .page-title {
        font-size: 1.75rem;
    }
}
</style>

