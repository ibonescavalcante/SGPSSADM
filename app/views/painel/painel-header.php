<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!--link rel="icon" href="/assets/logo.png" type="image/png"-->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            /* Cor primária */
            --color-primary: #679321;
            /* Variações da primária */
            --color-primary-light: #b8d67a;
            --color-primary-dark: #3c5b13;
            /* Cor complementar */
            --color-complementary: #921e8f;
            /* Tons neutros */
            --color-white: #ffffff;
            --color-gray-light: #f2f2f2;
            --color-gray: #cccccc;
            --color-black-soft: #222222;
        }

        body {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body>
    <header class="flex justify-between text-zinc-800 items-center py-4 px-9 border-b border-gray-300 bg-white">
        <div class="flex items-center gap-5">
            <div>
                <a href="painel/?page=inicio">
                    <img src="/assets/img/logo_prefeitura.svg" alt="logo" class="h-12" />
                </a>
            </div>

            <nav class="flex items-center gap-5 font-semibold text-lg">
                <ul>
                    <li>
                        <a href="painel" class="hover:text-green-800">Inicio</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="/recurso" class="hover:text-green-800">Recurso</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="/meusdados" class="hover:text-green-800">Meus Dados</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="/alterarsenha" class="hover:text-green-800">Alterar Senha</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="#" class="hover:text-green-800">Voltar para o site</a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="relative">
            <button id="user-menu-button" type="button" class="cursor-pointer flex items-center justify-center gap-2">
                Olá
                <span class="font-bold uppercase">

                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="#404040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-down-icon lucide-chevron-down">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>

            <div id="user-menu"
                class="hidden absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                <div class="py-1" role="none">
                    <a href="meus_dados.php" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100"
                        role="menuitem">
                        Meus Dados
                    </a>
                    <a href="alterar_senha.php" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100"
                        role="menuitem">
                        Alterar Senha
                    </a>

                    <a href="/logout" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Script para controlar o dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            if (userMenuButton && userMenu) {
                // Abre/fecha o menu ao clicar no botão
                userMenuButton.addEventListener('click', function(event) {
                    userMenu.classList.toggle('hidden');
                    event.stopPropagation();
                });

                // Fecha o menu se clicar em qualquer lugar fora dele
                window.addEventListener('click', function(event) {
                    if (!userMenu.classList.contains('hidden')) {
                        userMenu.classList.add('hidden');
                    }
                });

                // Impede que o menu feche ao clicar dentro dele
                userMenu.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }
        });
    </script>