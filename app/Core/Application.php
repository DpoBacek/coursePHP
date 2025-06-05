<?php
namespace WarehouseSystem\Core;

class Application
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->bootstrap();
    }

    private function bootstrap()
    {
        // Загрузка конфигурации
        $config = require $this->basePath . '/app/config/database.php';
        
        // Инициализация сессии
        session_name('WAREHOUSE_SESSION');
        session_start();
        
        // Инициализация базы данных
        $db = \WarehouseSystem\Utils\Database::getInstance($config);
        
        // Инициализация логгера
        $logger = new \WarehouseSystem\Utils\Logger('app.log');
        
        // Инициализация аутентификации
        \WarehouseSystem\Utils\Auth::init($db, $logger);
    }

    public function run()
    {
        // Простая маршрутизация
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Маршруты
        $routes = [
            '/' => 'DashboardController@index',
            '/login' => 'AuthController@login',
            '/logout' => 'AuthController@logout',
            '/products' => 'ProductController@index',
            // Добавьте другие маршруты
        ];

        // Поиск совпадения маршрута
        foreach ($routes as $route => $handler) {
            if ($route === $requestUri) {
                $this->handleRequest($handler);
                return;
            }
        }

        // Маршрут не найден
        http_response_code(404);
        echo 'Страница не найдена';
    }

    private function handleRequest(string $handler)
    {
        list($controllerName, $method) = explode('@', $handler);
        $controllerClass = "WarehouseSystem\\App\\Controllers\\{$controllerName}";
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            
            if (method_exists($controller, $method)) {
                $controller->$method();
                return;
            }
        }

        http_response_code(500);
        echo 'Ошибка сервера: неверный обработчик запроса';
    }
}