<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'warehouse_db';

// Подключаемся к серверу MySQL без выбора базы
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Ошибка подключения к MySQL: " . $conn->connect_error);
}

// Проверяем, существует ли база данных
$db_check = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($db_check->num_rows == 0) {
    // База не найдена — перенаправляем на установку
    header("Location: /includes/install.php");
    exit();
}

// Выбираем базу
$conn->select_db($dbname);

// Функция для проверки существования таблицы
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result && $result->num_rows > 0;
}

// Перечень обязательных таблиц
$required_tables = ['users', 'products', 'categories', 'locations', 'suppliers', 'clients', 'incoming', 'outgoing'];

// Проверяем, что все обязательные таблицы существуют
foreach ($required_tables as $table) {
    if (!tableExists($conn, $table)) {
        // Если хоть одной таблицы нет — перенаправляем на установку
        header("Location: /includes/install.php");
        exit();
    }
}

function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if ($result === false) {
        $_SESSION['error'] = "Ошибка SQL: " . $conn->error;
    }
    return $result;
}
?>