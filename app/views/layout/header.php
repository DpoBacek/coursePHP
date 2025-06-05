<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Складской учёт - <?= $title ?? 'Панель управления' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            min-height: calc(100vh - 56px);
            position: fixed;
            top: 56px;
            left: 0;
            z-index: 1000;
            padding: 20px 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--secondary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -var(--sidebar-width);
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
        }
        
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 2rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-box-seam"></i> Складской учёт
            </a>
            
            <div class="d-flex align-items-center">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Гость') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Боковое меню -->
    <div class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" href="/dashboard">
                    <i class="bi bi-speedometer2"></i> Панель управления
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'products' ? 'active' : '' ?>" href="/products">
                    <i class="bi bi-boxes"></i> Товары
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'incoming' ? 'active' : '' ?>" href="/incoming">
                    <i class="bi bi-arrow-down-circle"></i> Поступления
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'outgoing' ? 'active' : '' ?>" href="/outgoing">
                    <i class="bi bi-arrow-up-circle"></i> Отгрузки
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'reports' ? 'active' : '' ?>" href="/reports">
                    <i class="bi bi-bar-chart"></i> Отчёты
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $activeMenu === 'reference' ? 'active' : '' ?>" href="/reference">
                    <i class="bi bi-journal-bookmark"></i> Справочники
                </a>
            </li>
            
            <?php if (Auth::hasRole('admin')): ?>
                <li class="nav-item mt-3">
                    <span class="nav-link text-uppercase small fw-bold text-muted">Администрирование</span>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'users' ? 'active' : '' ?>" href="/users">
                        <i class="bi bi-people"></i> Пользователи
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'audit' ? 'active' : '' ?>" href="/audit">
                        <i class="bi bi-shield-check"></i> Журнал аудита
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- Основное содержимое -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid py-4">
            <!-- Заголовок страницы -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?= $pageTitle ?? 'Панель управления' ?></h1>
                
                <?php if (isset($pageActions)): ?>
                    <div class="btn-group">
                        <?= $pageActions ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Уведомления -->
            <?php include ROOT_DIR . '/app/views/partials/alerts.php'; ?>