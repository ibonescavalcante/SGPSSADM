<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Candidato - PSS</title>
    <link rel="icon" href="/assets/brasao.png" type="image/png">
    <link rel="shortcut icon" href="/assets/brasao.png" type="image/png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --color-primary: #679321;
            --color-primary-light: #b8d67a;
            --color-primary-dark: #3c5b13;
            --color-white: #ffffff;
            --color-gray-light: #f2f2f2;
            --color-gray: #cccccc;
            --color-black-soft: #222222;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="text-zinc-800 border-b border-gray-300 bg-white">
        <div class="flex flex-col lg:flex-row justify-between items-center py-3 px-4 lg:py-4 lg:px-9 gap-3 lg:gap-0">
            <!-- Logo e Navegação -->
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-5 w-full lg:w-auto">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/painel">
                        <img src="/assets/img/logo_prefeitura.svg" alt="logo" class="h-6 lg:h-8" />
                    </a>
                </div>

                <!-- Navegação -->
                <nav class="flex flex-wrap items-center justify-center gap-3 sm:gap-5 font-semibold text-sm lg:text-lg">
                    <a href="/painel" class="hover:text-green-800 whitespace-nowrap">Início</a>
                    <a href="/painel/meus-recursos" class="hover:text-green-800 whitespace-nowrap">Recursos</a>
                    <a href="/" class="hover:text-green-800 whitespace-nowrap">Voltar para o site</a>
                </nav>
            </div>

            <!-- Menu do Usuário -->
            <div class="relative">
                <button id="user-menu-button" type="button" class="cursor-pointer flex items-center justify-center gap-2 text-sm lg:text-base">
                    <span class="hidden sm:inline">Olá</span>
                    <span class="font-bold uppercase truncate max-w-[150px] lg:max-w-none">
                        <?php echo htmlspecialchars($_SESSION['usuario']['nome'] ?? 'Usuário'); ?>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" lg:width="20" lg:height="20" viewBox="0 0 24 24" fill="none"
                        stroke="#404040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-chevron-down flex-shrink-0">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>

            <div id="user-menu"
                class="hidden absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                <div class="py-1">
                    <a href="/painel/dados-usuario" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                        Meus Dados
                    </a>
                    <a href="/painel/alterar-senha" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                        Alterar Senha
                    </a>
                    <a href="/logout" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        <?= $this->section('content') ?>
    </main>

    <!-- JavaScript simples para o dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var userMenuButton = document.getElementById('user-menu-button');
            var userMenu = document.getElementById('user-menu');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    if (userMenu.classList.contains('hidden')) {
                        userMenu.classList.remove('hidden');
                    } else {
                        userMenu.classList.add('hidden');
                    }
                });

                document.addEventListener('click', function() {
                    userMenu.classList.add('hidden');
                });

                userMenu.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>

