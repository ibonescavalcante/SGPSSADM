<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SGPSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #1a73e8;
        --secondary: #6c757d;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --light: #f8f9fa;
        --dark: #343a40;
    }

    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        max-width: 500px;
        width: 100%;
        padding: 20px;
    }

    .login-card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .login-header {
        background-color: var(--primary);
        color: white;
        padding: 25px 20px;
        text-align: center;
    }

    .login-body {
        padding: 30px;
        background-color: white;
    }

    .form-control {
        border-radius: 5px;
        padding: 12px 15px;
        margin-bottom: 15px;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.25);
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 12px;
        font-weight: 600;
        border-radius: 5px;
    }

    .btn-primary:hover {
        background-color: #0d62d9;
        border-color: #0d62d9;
    }

    .login-footer {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .alert {
        border-radius: 5px;
        padding: 10px 15px;
        margin-bottom: 15px;
    }

    .system-info {
        text-align: center;
        margin-top: 20px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
    }

    .password-container {
        position: relative;
    }

    .brand-logo {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    </style>
</head>


<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <!--div class="brand-logo">
                    <i class="fas fa-file-alt brand-icon"></i>
                </div-->
                <h2>SGP</h2>
                <p class="mb-0">Sistema Gerenciador de Processo</p>
            </div>

            <div class="login-body">
                <?php if (isset($erro) && !empty($erro)): ?>
                <div id="loginAlert" class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="alertMessage"><?php echo htmlspecialchars($erro); ?></span>
                </div>
                <?php else: ?>
                <div id="loginAlert" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="alertMessage">Usuário ou senha incorretos.</span>
                </div>
                <?php endif; ?>

                <form id="loginForm" action="/dashboard/login" method="post" autocomplete="username">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>

                        <div class="input-group">
                            <span class="input-group-text" style="height: 49.5px;"><i class="fas fa-user"></i></span>

                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Digite seu usuário" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <div class="password-container">
                            <div class="input-group">
                                <span class="input-group-text" style="height: 49.5px;"><i
                                        class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Digite sua senha" required>
                            </div>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Lembrar-me</label>
                        <!--a href="#" class="float-end">Esqueci minha senha</a-->
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Entrar
                    </button>

                   
                </form>
            </div>

            <div class="login-footer">
                <p class="mb-0">© 2023 Sistema de Gerenciamento de Processos.</p>
            </div>
        </div>

        <div class="system-info">
            <p><i class="fas fa-shield-alt me-2"></i>Sistema seguro | <span class="badge bg-success">Online</span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });
        }

        const savedUsername = localStorage.getItem('savedUsername');
        const rememberMe = localStorage.getItem('rememberMe') === 'true';
        if (savedUsername && rememberMe) {
            var u = document.getElementById('username');
            var r = document.getElementById('rememberMe');
            if (u) u.value = savedUsername;
            if (r) r.checked = true;
        }

        var rememberEl = document.getElementById('rememberMe');
        if (rememberEl) {
            rememberEl.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('rememberMe', 'true');
                } else {
                    localStorage.setItem('rememberMe', 'false');
                    localStorage.removeItem('savedUsername');
                }
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                var remember = document.getElementById('rememberMe');
                if (remember && remember.checked) {
                    var username = document.getElementById('username');
                    if (username && username.value) {
                        localStorage.setItem('savedUsername', username.value);
                    }
                }
            });
        }
    });
    </script>
</body>

</html>