<?php
define('ROOT_DIR', __DIR__);
// Автозагрузка классов
spl_autoload_register(function($class) {
    // Преобразование пространства имён в путь к файлу
    $file = ROOT_DIR . '/app/utils/' . str_replace('\\', '/', $class) . '.php';
    require $file;
});

// Загрузка конфигурации
$config = require ROOT_DIR . '/app/config/database.php';

// Инициализация сессии
session_start();

// Базовая маршрутизация
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => 'app/views/dashboard/index.php',
    '/login' => 'app/views/auth/login.php',
    '/products' => 'app/views/products/index.php',
    '/reports' => 'app/views/reports/inventory.php',
];

if (isset($routes[$request])) {
    require ROOT_DIR . '/' . $routes[$request];
} else {
    http_response_code(404);
    echo 'Страница не найдена';
}