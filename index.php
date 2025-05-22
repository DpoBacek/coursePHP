<?php
session_start();
include 'includes/db.php';

include 'includes/header.php';
?>

<h1>Добро пожаловать в систему складского учёта</h1>

<?php
// Вывод сообщений об успехе
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
?>
        <h2>Панель управления</h2>
        <div class="row mt-4">
            <?php
            // Запросы для статистики
            $res1 = $conn->query("SELECT COUNT(*) AS total FROM products");
            $products_count = $res1->fetch_assoc()['total'];

            $res2 = $conn->query("SELECT COUNT(*) AS total FROM incoming");
            $incoming_count = $res2->fetch_assoc()['total'];

            $res3 = $conn->query("SELECT COUNT(*) AS total FROM outgoing");
            $outgoing_count = $res3->fetch_assoc()['total'];
            ?>
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Товары на складе</h5>
                        <p class="card-text fs-3"><?= $products_count ?></p>
                        <a href="products.php" class="btn btn-light btn-sm">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Поступлений</h5>
                        <p class="card-text fs-3"><?= $incoming_count ?></p>
                        <a href="incoming.php" class="btn btn-light btn-sm">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Отгрузок</h5>
                        <p class="card-text fs-3"><?= $outgoing_count ?></p>
                        <a href="outgoing.php" class="btn btn-light btn-sm">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>