<?php
/**
 * includes/install.php
 * Полная установка базы данных для системы складского учёта
 */
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'warehouse_db';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    $_SESSION['errors'][] = 'Ошибка подключения: ' . $conn->connect_error;
    header('Location: /index.php'); exit();
}

if (!$conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
    $_SESSION['errors'][] = 'Ошибка создания БД: ' . $conn->error;
    header('Location: /index.php'); exit();
}

$conn->select_db($dbname);

function tableExists(mysqli $c, string $name): bool {
    return (bool)$c->query("SHOW TABLES LIKE '$name'")->num_rows;
}

function tableIsEmpty(mysqli $c, string $name): bool {
    $res = $c->query("SELECT COUNT(*) AS cnt FROM $name");
    return ($res && $res->fetch_assoc()['cnt'] ?? 0) == 0;
}

$adminHash = password_hash('admin123', PASSWORD_DEFAULT);

$schema = [
    'users' => [
        "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255),
            role ENUM('admin','manager') DEFAULT 'manager',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        ["INSERT INTO users (username, password, role) VALUES ('admin', '$adminHash', 'admin')"]
    ],

    'categories' => [
        "CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )",
        ["INSERT INTO categories (name) VALUES ('Техника'), ('Расходники'), ('Упаковка')"]
    ],

    'suppliers' => [
        "CREATE TABLE suppliers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            contact_info TEXT
        )",
        ["INSERT INTO suppliers (name, contact_info) VALUES
            ('ООО Снаб', 'snab@example.com'),
            ('ИмпортТрейд', 'import@example.com')"]
    ],

    'clients' => [
        "CREATE TABLE clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            contact_info TEXT
        )",
        ["INSERT INTO clients (name, contact_info) VALUES
            ('Клиент 1', 'client1@mail.com'),
            ('Клиент 2', 'client2@mail.com')"]
    ],

    'locations' => [
        "CREATE TABLE locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50)
        )",
        ["INSERT INTO locations (name) VALUES ('Зона A'), ('Зона B')"]
    ],

    'warehouses' => [
        "CREATE TABLE warehouses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            location VARCHAR(150)
        )",
        ["INSERT INTO warehouses (name, location) VALUES
            ('Центральный', 'г. Москва'),
            ('Склад 2', 'г. Казань')"]
    ],

    'products' => [
        "CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            category_id INT,
            supplier_id INT,
            location_id INT,
            quantity INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id),
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
            FOREIGN KEY (location_id) REFERENCES locations(id)
        )",
        ["INSERT INTO products (name, category_id, supplier_id, location_id, quantity) VALUES
            ('Сканер', 1, 1, 1, 50),
            ('Пленка стрейч', 2, 2, 2, 200)"]
    ],

    'incoming' => [
        "CREATE TABLE incoming (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            supplier_id INT,
            warehouse_id INT,
            quantity INT,
            received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
            FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
        )",
        ["INSERT INTO incoming (product_id, supplier_id, warehouse_id, quantity) VALUES (1,1,1,30)"]
    ],

    'outgoing' => [
        "CREATE TABLE outgoing (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            client_id INT,
            warehouse_id INT,
            quantity INT,
            sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (client_id) REFERENCES clients(id),
            FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
        )",
        ["INSERT INTO outgoing (product_id, client_id, warehouse_id, quantity) VALUES (2,2,2,20)"]
    ],

    'inventory_logs' => [
        "CREATE TABLE inventory_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            change_type ENUM('incoming','outgoing','manual'),
            quantity INT,
            user_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        []
    ],
];

foreach ($schema as $table => [$ddl, $seeds]) {
    if (!tableExists($conn, $table)) {
        if ($conn->query($ddl)) {
            $_SESSION['success'][] = "Таблица <b>$table</b> создана.";
        } else {
            $_SESSION['errors'][] = "Ошибка создания $table: " . $conn->error;
            continue;
        }
    } else {
        $_SESSION['success'][] = "Таблица <b>$table</b> уже существует.";
    }

    if ($seeds && tableIsEmpty($conn, $table)) {
        foreach ($seeds as $sql) {
            if ($conn->query($sql)) {
                $_SESSION['success'][] = "<b>$table</b> — данные добавлены.";
            } else {
                $_SESSION['errors'][] = "Ошибка вставки в $table: " . $conn->error;
            }
        }
    }
}

$conn->close();

if (empty($_SESSION['errors'])) {
    $_SESSION['success'][] = 'Установка завершена успешно.';
} else {
    $_SESSION['errors'][] = 'Установка завершена с ошибками. См. выше.';
}

header('Location: /index.php');
exit();