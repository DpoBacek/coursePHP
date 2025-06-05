<?php
// Настройки безопасности
return [
    // Настройки сессии
    'session' => [
        'name' => 'WAREHOUSE_SESSID',
        'lifetime' => 3600, // 1 час
        'httponly' => true,
        'secure' => true, // Только HTTPS
        'samesite' => 'Strict'
    ],
    
    // Настройки CSRF
    'csrf' => [
        'token_name' => 'csrf_token',
        'regenerate' => true // Регенерировать токен после каждого использования
    ],
    
    // Настройки паролей
    'password' => [
        'algorithm' => PASSWORD_BCRYPT,
        'options' => ['cost' => 12]
    ],
    
    // Лимиты попыток входа
    'auth' => [
        'max_attempts' => 5,
        'lockout_time' => 300 // 5 минут блокировки
    ]
];