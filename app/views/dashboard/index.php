<?php
$pageTitle = 'Панель управления';
$activeMenu = 'dashboard';
include __DIR__ . '/../layout/header.php';
if (isset($routes[$request_path])) {
    $view_file = ROOT_DIR . '/' . $routes[$request_path];
    
    if (file_exists($view_file)) {
        // Инициализация зависимостей
        $db = new Database($config);
        $productRepo = new ProductService($db);
        $incomingRepo = new IncomingService($db);
        $outgoingRepo = new OutgoingService($db);
        $movementRepo = new MovementService($db);
        $auditRepo = new AuditService($db);
        
        // Создание контроллера
        $controller = new DashboardController(
            $productRepo,
            $incomingRepo,
            $outgoingRepo,
            $movementRepo,
            $auditRepo
        );
        
        // Вызов метода контроллера
        $controller->index();
    } else {
        // ... обработка ошибки
    }
} else {
    // ... 404
}
?>

<div class="row">
    <!-- Карточка статистики: Товары -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-primary">Товары</h5>
                        <h2 class="card-text"><?= number_format($stats['total_products']) ?></h2>
                        <p class="card-text"><small class="text-muted">На складе</small></p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-box text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Карточка статистики: Поступления -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-success">Поступления</h5>
                        <h2 class="card-text"><?= number_format($stats['month_incoming']) ?></h2>
                        <p class="card-text"><small class="text-muted">За месяц</small></p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-arrow-down-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Карточка статистики: Отгрузки -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-info">Отгрузки</h5>
                        <h2 class="card-text"><?= number_format($stats['month_outgoing']) ?></h2>
                        <p class="card-text"><small class="text-muted">За месяц</small></p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-arrow-up-circle text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Карточка статистики: Низкие остатки -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-danger">Низкие остатки</h5>
                        <h2 class="card-text"><?= number_format($stats['low_stock']) ?></h2>
                        <p class="card-text"><small class="text-muted">Требуют пополнения</small></p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- График движения товаров -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Движение товаров за последний месяц</h5>
            </div>
            <div class="card-body">
                <canvas id="movementsChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Последние действия -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Последние действия</h5>
                <a href="/audit" class="btn btn-sm btn-outline-primary">Все</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentActivities as $activity): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($activity['description']) ?></h6>
                                <small class="text-muted"><?= $activity['username'] ?></small>
                            </div>
                            <small class="text-muted"><?= formatDateTime($activity['created_at']) ?></small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Товары с низким остатком -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Товары с низким остатком</h5>
                <a href="/products?filter=low_stock" class="btn btn-sm btn-outline-danger">Все</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Местоположение</th>
                                <th class="text-end">Остаток</th>
                                <th class="text-end">Минимум</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td>
                                    <a href="/products/view/<?= $product['id'] ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($product['location_name']) ?></td>
                                <td class="text-end text-danger fw-bold"><?= $product['quantity'] ?></td>
                                <td class="text-end"><?= $product['min_quantity'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Предстоящие поставки -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Предстоящие поставки</h5>
                <a href="/incoming" class="btn btn-sm btn-outline-primary">Все</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Поставщик</th>
                                <th>Товар</th>
                                <th class="text-end">Количество</th>
                                <th class="text-end">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingIncoming as $incoming): ?>
                            <tr>
                                <td><?= htmlspecialchars($incoming['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($incoming['product_name']) ?></td>
                                <td class="text-end"><?= $incoming['quantity'] ?></td>
                                <td class="text-end"><?= formatDate($incoming['expected_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$customScripts = <<<JS
<script>
    // График движения товаров
    const ctx = document.getElementById('movementsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$chartLabels},
            datasets: [
                {
                    label: 'Поступления',
                    data: {$incomingData},
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Отгрузки',
                    data: {$outgoingData},
                    borderColor: 'rgb(23, 162, 184)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Количество'
                    }
                }
            }
        }
    });
</script>
JS;

include __DIR__ . '/../layout/footer.php';
?>