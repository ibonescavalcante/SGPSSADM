<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PSS Platform' ?></title>
    <link rel="icon" href="/assets/brasao.png" type="image/png">
    <link rel="shortcut icon" href="/assets/brasao.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    :root {
        /* Cores da prefeitura */
        --color-primary: #376d2b;
        --color-primary-light: #4a8a3a;
        --color-primary-dark: #2f5c26;
        --color-complementary: #921e8f;
        --color-white: #ffffff;
        --color-gray-light: #fafaf9;
        --color-gray: #e0e0e0;
        --color-black-soft: #1b1917;
    }

    body {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
        background: var(--color-gray-light);
        color: var(--color-black-soft);
    }

    /* Estilos para os botões */
    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-primary {
        background-color: var(--color-primary);
        color: var(--color-white);
    }

    .btn-primary:hover {
        background-color: var(--color-primary-dark);
    }

    .btn-secondary {
        background-color: var(--color-gray);
        color: var(--color-black-soft);
    }

    .btn-secondary:hover {
        background-color: #c0c0c0;
    }
    </style>
</head>

<body>
    <header class="bg-white shadow-sm py-3 px-4 md:py-4 md:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-0">
            <!-- Logo e Título -->
            <div class="flex items-center">
                <a href="/"><img src="/assets/img/logo_prefeitura.svg" alt="Logo da Prefeitura"
                        class="h-6 md:h-8 mr-2 md:mr-4"></a>
                <h1 class="text-lg md:text-2xl font-bold" style="color: var(--color-primary);">PSS Platform</h1>
            </div>

            <!-- Menu de Navegação -->
            <div class="flex flex-col sm:flex-row items-center gap-2 md:gap-4 w-full md:w-auto">
                <?php if (isset($_SESSION['usuario'])): ?>
                <!-- Usuário Logado -->
                <div class="flex flex-col sm:flex-row items-center gap-2 md:gap-4 w-full sm:w-auto">
                    <span class="text-gray-700 text-sm md:text-base text-center">
                        Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>
                    </span>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <a href="/painel" class="btn btn-secondary flex-1 sm:flex-none text-xs md:text-sm">Meu
                            Painel</a>
                        <a href="/logout" class="btn btn-secondary flex-1 sm:flex-none text-xs md:text-sm">Sair</a>
                    </div>
                </div>
                <?php else: ?>
                <!-- Usuário Não Logado -->
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="/login" class="btn btn-secondary flex-1 sm:flex-none text-xs md:text-sm">Entrar</a>
                    <a href="/cadastro" class="btn btn-primary flex-1 sm:flex-none text-xs md:text-sm">Cadastrar</a>
                </div>
                <?php endif; ?>
                <a href="/admin" class="btn btn-primary w-full sm:w-auto text-xs md:text-sm">Área Administrativa</a>
            </div>
        </div>
    </header>
    <main>
        <?= $this->section('content') ?>
    </main>
    <!--footer
        class="fixed bottom-0 left-0 w-full bg-[rgb(103,147,33)] text-white opacity-[40%] py-2 px-8 text-center z-50">
        <div class="mb-2">&copy; 2024 VCarbo. Todos os direitos reservados.</div>
        <div class="text-xs text-[#d1caa4]">VCarbo.</div>
    </footer-->
    <script>
    function mascaraCPF(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede digitar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }
        i.setAttribute("maxlength", "14");
        if (v.length == 3 || v.length == 7) i.value += ".";
        if (v.length == 11) i.value += "-";
    }

    function mascaraCEP(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede digitar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }
        i.setAttribute("maxlength", "9");
        if (v.length == 5) i.value += "-";
    }

    function mascaraCelular(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede digitar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }
        i.setAttribute("maxlength", "15");
        if (v.length == 1) i.value = "(" + v;
        if (v.length == 3) i.value += ") ";
        if (v.length == 10) i.value += "-";
    }

    function mascaraData(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede digitar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }
        i.setAttribute("maxlength", "10");
        if (v.length == 2 || v.length == 5) i.value += "/";
    }
    </script>
</body>

</html>