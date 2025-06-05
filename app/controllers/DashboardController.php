<?php
class DashboardController
{
    public function index()
    {
        // Проверка аутентификации
        \app\utils\Auth::requireLogin();

        // Подключение представления
        $viewData = [
            'pageTitle' => 'Панель управления',
            'activeMenu' => 'dashboard',
            'stats' => $stats
        ];
        // 1. Получаем статистику
        $stats = [
            'total_products' => $this->productRepository->getTotalCount(),
            'month_incoming' => $this->incomingRepository->getMonthTotal(),
            'month_outgoing' => $this->outgoingRepository->getMonthTotal(),
            'low_stock' => $this->productRepository->getLowStockCount()
        ];
        
        // 2. Последние активности
        $recentActivities = $this->auditLogRepository->getRecentActivities(5);
        
        // 3. Товары с низким остатком
        $lowStockProducts = $this->productRepository->getLowStockProducts(5);
        
        // 4. Предстоящие поставки
        $upcomingIncoming = $this->incomingRepository->getUpcomingIncoming(5);
        
        // 5. Данные для графика
        $chartData = $this->movementRepository->getLast30DaysMovement();
        $chartLabels = json_encode(array_column($chartData, 'date'));
        $incomingData = json_encode(array_column($chartData, 'incoming'));
        $outgoingData = json_encode(array_column($chartData, 'outgoing'));
        
        // 6. Подключаем представление
        extract($viewData);
        require ROOT_DIR . '/app/views/dashboard/index.php';

        
    }
}