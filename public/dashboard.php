<?php
session_start();
require_once 'includes/db.php';
include 'includes/header.php';

// Проверяем, авторизован ли пользователь
if (empty($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
if (!empty($_SESSION['success'])) {
    foreach ($_SESSION['success'] as $msg) {
        echo '<div class="alert alert-success">' . $msg . '</div>';
    }
    unset($_SESSION['success']);
}

// Вывод сообщений об ошибках
if (!empty($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $msg) {
        echo '<div class="alert alert-danger">' . $msg . '</div>';
    }
    unset($_SESSION['errors']);
}
try {
    // Получаем статистику
    $products_count = executeQuery($conn, "SELECT COUNT(*) AS cnt FROM products")
                     ->fetch_assoc()['cnt'] ?? 0;

    $suppliers_count = executeQuery($conn, "SELECT COUNT(*) AS cnt FROM suppliers")
                      ->fetch_assoc()['cnt'] ?? 0;

    $clients_count = executeQuery($conn, "SELECT COUNT(*) AS cnt FROM clients")
                    ->fetch_assoc()['cnt'] ?? 0;

    $users_count = executeQuery($conn, "SELECT COUNT(*) AS cnt FROM users")
                  ->fetch_assoc()['cnt'] ?? 0;

    $incoming = [];
    $outgoing = [];

    // Получаем последние операции
    $incomingResult = executeQuery($conn, 
        "SELECT i.id, p.name AS product_name, i.quantity, i.received_at 
         FROM incoming i 
         JOIN products p ON i.product_id = p.id 
         ORDER BY i.received_at DESC LIMIT 5");
    $incoming = $incomingResult;

    $outgoingResult = executeQuery($conn, 
        "SELECT o.id, p.name AS product_name, o.quantity, o.sent_at
         FROM outgoing o 
         JOIN products p ON o.product_id = p.id 
         ORDER BY o.sent_at DESC LIMIT 5");
    $outgoing = $outgoingResult;


} catch (Exception $e) {
    $_SESSION['errors'][] = "Ошибка при работе с базой данных.";
}

?>
<style>
    .table-fixed td, .table-fixed th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .table-fixed td:nth-child(1), .table-fixed td:nth-child(3) {
        min-width: 200px;
    }
    
    .table-fixed td:nth-child(2) {
        min-width: 100px;
    }
</style>
    <h1>Dashboard</h1>
        <div class="row mt-5">
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-header">Пользователи</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $users_count ?></h5>
                        <p class="card-text">Зарегистрировано в системе</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Товары</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $products_count ?></h5>
                        <p class="card-text">Всего товаров на складе</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Поставщики</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $suppliers_count ?></h5>
                        <p class="card-text">Всего поставщиков</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Клиенты</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $clients_count ?></h5>
                        <p class="card-text">Всего клиентов</p>
                    </div>
                </div>
            </div>
        </div>
        
    <?php if (isset($incoming)): ?>
        <h3 class="mt-5">Последние приходы</h3>
            <table class="table table-striped table-fixed">
                <thead>
                    <tr><th>Товар</th><th>Количество</th><th>Дата</th></tr>
                </thead>
                <tbody>
                <?php while($row = $incoming->fetch_assoc()): ?>
                    <tr>
                        <td><?=htmlspecialchars($row['product_name'])?></td>
                        <td><?= (int)$row['quantity'] ?></td>
                        <td><?=htmlspecialchars($row['received_at'])?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
    <?php endif; ?>
    <?php if (isset($outgoing)): ?>
        <div class="col-12">
        <h3 class="mt-5">Последние расходы</h3>
            <table class="table table-striped table-fixed">
                <thead>
                    <tr><th>Товар</th><th>Количество</th><th>Дата</th></tr>
                </thead>
                <tbody>
                <?php while($row = $outgoing->fetch_assoc()): ?>
                    <tr>
                        <td><?=htmlspecialchars($row['product_name'])?></td>
                        <td><?= (int)$row['quantity'] ?></td>
                        <td><?=htmlspecialchars($row['sent_at'])?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>