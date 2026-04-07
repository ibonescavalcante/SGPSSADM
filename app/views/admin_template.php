<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Administração - PSS Platform') ?></title>
    <link rel="icon" href="/assets/brasao.png" type="image/png">
    <link rel="shortcut icon" href="/assets/brasao.png" type="image/png">
    
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Principal -->
    <style>
        :root {
            /* Cores Verdes - Tema Principal */
            --color-primary: #2E7D32;
            --color-primary-light: #388E3C;
            --color-primary-dark: #1B5E20;
            --color-accent: #4CAF50;
            --color-success: #4CAF50;
            --color-warning: #FF9800;
            --color-error: #F44336;
            --color-info: #2196F3;
            
            /* Cores Neutras */
            --color-white: #FFFFFF;
            --color-gray-50: #FAFAFA;
            --color-gray-100: #F5F5F5;
            --color-gray-200: #EEEEEE;
            --color-gray-300: #E0E0E0;
            --color-gray-400: #BDBDBD;
            --color-gray-500: #9E9E9E;
            --color-gray-600: #757575;
            --color-gray-700: #616161;
            --color-gray-800: #424242;
            --color-gray-900: #212121;
            
            /* Cores de Texto */
            --color-text: var(--color-gray-800);
            --color-text-light: var(--color-gray-600);
            --color-text-muted: var(--color-gray-500);
            
            /* Cores de Fundo */
            --color-background: var(--color-gray-50);
            --color-surface: var(--color-white);
            --color-border: var(--color-gray-200);
            
            /* Layout */
            --admin-sidebar-width: 280px;
            --admin-sidebar-collapsed-width: 80px;
            --admin-header-height: 80px;
            
            /* Sombras */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Transições */
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--color-background);
            color: var(--color-text);
            line-height: 1.6;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: linear-gradient(180deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            transition: all var(--transition-normal);
            box-shadow: var(--shadow-xl);
        }

        .admin-sidebar.collapsed {
            width: var(--admin-sidebar-collapsed-width);
        }

        .admin-sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        .admin-main {
            flex: 1;
            margin-left: var(--admin-sidebar-width);
            transition: margin-left var(--transition-normal);
        }

        .admin-main.sidebar-collapsed {
            margin-left: var(--admin-sidebar-collapsed-width);
        }

        .admin-main.sidebar-mobile-hidden {
            margin-left: 0;
        }

        /* Header da Sidebar */
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            background: rgba(255,255,255,0.05);
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .sidebar-header {
            padding: 1.5rem 0.75rem;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .sidebar-logo {
            flex-direction: column;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--color-white);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 1.25rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .logo-text {
            font-size: 0.75rem;
            line-height: 1;
        }

        .sidebar-subtitle {
            font-size: 0.875rem;
            opacity: 0.8;
            margin: 0;
            font-weight: 400;
            white-space: nowrap;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .sidebar-subtitle {
            font-size: 0.625rem;
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: absolute;
            top: 1.5rem;
            right: -15px;
            width: 30px;
            height: 30px;
            background: var(--color-white);
            border: 2px solid var(--color-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--color-primary);
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            z-index: 1001;
        }

        .sidebar-toggle:hover {
            background: var(--color-primary);
            color: white;
            transform: scale(1.1);
        }

        .admin-sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }

        /* Navegação da Sidebar */
        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            padding: 0 1.5rem 0.75rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.7;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .nav-section-title {
            padding: 0 0.75rem 0.75rem;
            font-size: 0;
            height: 0;
            margin-bottom: -0.75rem;
        }

        .nav-item {
            margin: 0.25rem 0;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .admin-sidebar.collapsed .nav-link {
            padding: 0.875rem 0.75rem;
            justify-content: center;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            border-left-color: var(--color-accent);
            color: white;
            transform: translateX(4px);
        }

        .admin-sidebar.collapsed .nav-link:hover,
        .admin-sidebar.collapsed .nav-link.active {
            transform: none;
            border-left: none;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.875rem;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .admin-sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .nav-link-text {
            white-space: nowrap;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .nav-link-text {
            width: 0;
            opacity: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--color-accent);
            color: var(--color-primary-dark);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
            transition: all var(--transition-normal);
        }

        .admin-sidebar.collapsed .nav-badge {
            display: none;
        }

        /* Tooltip para sidebar colapsada */
        .nav-tooltip {
            position: absolute;
            left: calc(100% + 15px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--color-gray-900);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-fast);
            z-index: 1002;
            pointer-events: none;
        }

        .nav-tooltip::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: var(--color-gray-900);
        }

        .admin-sidebar.collapsed .nav-item:hover .nav-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Header Principal */
        .admin-header {
            background: var(--color-white);
            height: var(--admin-header-height);
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .desktop-menu-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--color-primary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background var(--transition-fast);
        }

        .desktop-menu-toggle:hover {
            background: var(--color-gray-100);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--color-primary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background var(--transition-fast);
        }

        .mobile-menu-toggle:hover {
            background: var(--color-gray-100);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .breadcrumb-item {
            color: var(--color-text-light);
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: var(--color-primary);
            font-weight: 600;
        }

        .breadcrumb-separator {
            color: var(--color-text-muted);
        }

        /* Menu do Usuário */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
            margin-right: 0.5rem;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--color-text);
            margin: 0;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--color-text-light);
            margin: 0;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.125rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            border: 3px solid var(--color-white);
            box-shadow: var(--shadow-md);
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
            min-width: 220px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all var(--transition-fast);
        }

        .dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid var(--color-border);
            background: var(--color-gray-50);
            border-radius: 12px 12px 0 0;
        }

        .dropdown-user-name {
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }

        .dropdown-user-email {
            font-size: 0.875rem;
            color: var(--color-text-light);
            margin: 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: var(--color-text);
            text-decoration: none;
            border-bottom: 1px solid var(--color-border);
            transition: background var(--transition-fast);
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: var(--color-gray-50);
        }

        .dropdown-item:last-child {
            border-bottom: none;
            border-radius: 0 0 12px 12px;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
            color: var(--color-text-light);
        }

        .dropdown-item.danger {
            color: var(--color-error);
        }

        .dropdown-item.danger i {
            color: var(--color-error);
        }

        /* Conteúdo Principal */
        .admin-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Títulos e Headers */
        .admin-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: var(--color-primary);
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-subtitle {
            color: var(--color-text-light);
            margin: 0 0 2rem 0;
            font-size: 1.125rem;
            font-weight: 400;
        }

        /* Cards */
        .card {
            background: var(--color-white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--color-border);
            transition: all var(--transition-fast);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: var(--color-text);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
            font-size: 0.875rem;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--color-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background: transparent;
            color: var(--color-primary);
            border: 1px solid var(--color-primary);
        }

        .btn-outline:hover {
            background: var(--color-primary);
            color: white;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }

        /* Responsividade */
        @media (max-width: 1024px) {
            .desktop-menu-toggle {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.mobile-show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .admin-content {
                padding: 1rem;
            }

            .admin-header {
                padding: 0 1rem;
            }

            .user-info {
                display: none;
            }

            .admin-title {
                font-size: 1.875rem;
            }

            .breadcrumb {
                display: none;
            }
        }

        /* Overlay para mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            transition: opacity var(--transition-normal);
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Utilitários */
        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .gap-4 {
            gap: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .text-gray-500 {
            color: var(--color-gray-500);
        }

        /* Animações suaves */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .nav-item {
            animation: slideIn 0.3s ease forwards;
        }

        .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .nav-item:nth-child(2) { animation-delay: 0.2s; }
        .nav-item:nth-child(3) { animation-delay: 0.3s; }
        .nav-item:nth-child(4) { animation-delay: 0.4s; }
        .nav-item:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Toggle Button -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h1 class="logo-text">PSS Admin</h1>
                </div>
                <p class="sidebar-subtitle">Prefeitura Municipal</p>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <div class="nav-item">
                        <a href="/admin" class="nav-link <?= $this->getCurrentPage() === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-link-text">Dashboard</span>
                            <div class="nav-tooltip">Dashboard</div>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">PSS</div>
                    <div class="nav-item">
                        <a href="/admin/pss" class="nav-link <?= $this->getCurrentPage() === 'gerenciar-pss' || $this->getCurrentPage() === 'pss' ? 'active' : '' ?>">
                            <i class="fas fa-file-alt"></i>
                            <span class="nav-link-text">Gerenciar PSS</span>
                            <div class="nav-tooltip">Gerenciar PSS</div>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/pss/criar-novo" class="nav-link <?= $this->getCurrentPage() === 'criar-novo' ? 'active' : '' ?>">
                            <i class="fas fa-plus"></i>
                            <span class="nav-link-text">Novo PSS</span>
                            <div class="nav-tooltip">Novo PSS</div>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Candidatos</div>
                    <div class="nav-item">
                        <a href="/admin/inscricoes" class="nav-link <?= $this->getCurrentPage() === 'inscricoes' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i>
                            <span class="nav-link-text">Inscrições</span>
                            <div class="nav-tooltip">Inscrições</div>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/candidatos" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span class="nav-link-text">Candidatos</span>
                            <div class="nav-tooltip">Candidatos</div>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Validação</div>
                    <div class="nav-item">
                        <a href="/admin/documentos/pendentes" class="nav-link">
                            <i class="fas fa-file-check"></i>
                            <span class="nav-link-text">Documentos</span>
                            <?php if (($documentos_pendentes ?? 0) > 0): ?>
                                <span class="nav-badge"><?= $documentos_pendentes ?></span>
                            <?php endif; ?>
                            <div class="nav-tooltip">Documentos</div>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/recursos/pendentes" class="nav-link">
                            <i class="fas fa-reply"></i>
                            <span class="nav-link-text">Recursos</span>
                            <?php if (($recursos_pendentes ?? 0) > 0): ?>
                                <span class="nav-badge"><?= $recursos_pendentes ?></span>
                            <?php endif; ?>
                            <div class="nav-tooltip">Recursos</div>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Avaliação</div>
                    <div class="nav-item">
                        <a href="/admin/avaliacoes" class="nav-link <?= $this->getCurrentPage() === 'avaliacoes' ? 'active' : '' ?>">
                            <i class="fas fa-star"></i>
                            <span class="nav-link-text">Avaliações</span>
                            <div class="nav-tooltip">Avaliações</div>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/classificacao" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-link-text">Classificação</span>
                            <div class="nav-tooltip">Classificação</div>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Relatórios</div>
                    <div class="nav-item">
                        <a href="/admin/relatorios" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-link-text">Relatórios</span>
                            <div class="nav-tooltip">Relatórios</div>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/admin/exportar" class="nav-link">
                            <i class="fas fa-download"></i>
                            <span class="nav-link-text">Exportar Dados</span>
                            <div class="nav-tooltip">Exportar Dados</div>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Overlay para mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Conteúdo Principal -->
        <main class="admin-main" id="adminMain">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="desktop-menu-toggle" id="desktopMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <?php if (isset($breadcrumb)): ?>
                        <nav class="breadcrumb">
                            <?php foreach ($breadcrumb as $index => $item): ?>
                                <?php if ($index > 0): ?>
                                    <span class="breadcrumb-separator">/</span>
                                <?php endif; ?>
                                <span class="breadcrumb-item <?= $index === count($breadcrumb) - 1 ? 'active' : '' ?>">
                                    <?= htmlspecialchars($item) ?>
                                </span>
                            <?php endforeach; ?>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="user-menu">
                    <div class="user-info">
                        <p class="user-name"><?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?></p>
                        <p class="user-role">Administrador</p>
                    </div>
                    
                    <div class="dropdown" id="userDropdown">
                        <div class="user-avatar" onclick="toggleDropdown('userDropdown')">
                            <?= strtoupper(substr($_SESSION['admin_nome'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <p class="dropdown-user-name"><?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?></p>
                                <p class="dropdown-user-email"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@prefeitura.gov.br') ?></p>
                            </div>
                            <a href="/admin/perfil" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                Meu Perfil
                            </a>
                            <a href="/admin/alterar-senha" class="dropdown-item">
                                <i class="fas fa-key"></i>
                                Alterar Senha
                            </a>
                            <a href="/admin/configuracoes" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Configurações
                            </a>
                            <a href="/admin/logout" class="dropdown-item danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Sair do Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <div class="admin-content">
                <?= $this->section('content') ?>
            </div>
        </main>
    </div>

    <script>
        // Estado do sidebar
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        // Elementos
        const sidebar = document.getElementById('adminSidebar');
        const adminMain = document.getElementById('adminMain');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const desktopMenuToggle = document.getElementById('desktopMenuToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Inicializar estado do sidebar
        function initSidebar() {
            if (window.innerWidth > 1024) {
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    adminMain.classList.add('sidebar-collapsed');
                }
            } else {
                sidebar.classList.add('mobile-hidden');
                adminMain.classList.add('sidebar-mobile-hidden');
            }
        }

        // Toggle sidebar desktop
        function toggleSidebarDesktop() {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                adminMain.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                adminMain.classList.remove('sidebar-collapsed');
            }
        }

        // Toggle sidebar mobile
        function toggleSidebarMobile() {
            const isHidden = sidebar.classList.contains('mobile-hidden');
            
            if (isHidden) {
                sidebar.classList.remove('mobile-hidden');
                sidebar.classList.add('mobile-show');
                sidebarOverlay.classList.add('show');
            } else {
                sidebar.classList.add('mobile-hidden');
                sidebar.classList.remove('mobile-show');
                sidebarOverlay.classList.remove('show');
            }
        }

        // Event listeners
        sidebarToggle.addEventListener('click', toggleSidebarDesktop);
        desktopMenuToggle.addEventListener('click', toggleSidebarDesktop);
        mobileMenuToggle.addEventListener('click', toggleSidebarMobile);

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.add('mobile-hidden');
            sidebar.classList.remove('mobile-show');
            sidebarOverlay.classList.remove('show');
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                // Desktop mode
                sidebar.classList.remove('mobile-hidden', 'mobile-show');
                adminMain.classList.remove('sidebar-mobile-hidden');
                sidebarOverlay.classList.remove('show');
                
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    adminMain.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                    adminMain.classList.remove('sidebar-collapsed');
                }
            } else {
                // Mobile mode
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('mobile-hidden');
                adminMain.classList.remove('sidebar-collapsed');
                adminMain.classList.add('sidebar-mobile-hidden');
                sidebarOverlay.classList.remove('show');
            }
        });

        // Toggle dropdown
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();
        });
    </script>
</body>
</html>

