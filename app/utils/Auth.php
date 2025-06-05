<?php
class Auth {
    private static $db;
    private static $logger;
    
    public static function init(Database $db, Logger $logger) {
        self::$db = $db;
        self::$logger = $logger;
    }
    
    public static function attemptLogin(string $username, string $password): bool {
        $user = self::$db->fetch(
            "SELECT id, username, password, role FROM users WHERE username = ?", 
            [$username]
        );
        
        if (!$user) {
            self::$logger->warning("Login attempt failed for non-existent user: $username");
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            
            self::$logger->info("User logged in: {$user['username']} ({$user['id']})");
            return true;
        }
        
        self::$logger->warning("Invalid password for user: $username");
        return false;
    }
    
    public static function check(): bool {
        return isset($_SESSION['user']);
    }
    
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }
    
    public static function id(): ?int {
        return $_SESSION['user']['id'] ?? null;
    }
    
    public static function role(): ?string {
        return $_SESSION['user']['role'] ?? null;
    }
    
    public static function logout() {
        if (self::check()) {
            self::$logger->info("User logged out: {$_SESSION['user']['username']}");
            unset($_SESSION['user']);
        }
        session_destroy();
    }
    
    public static function hasRole(string $role): bool {
        return self::role() === $role;
    }
    
    public static function requireRole(string $role) {
        if (!self::check() || !self::hasRole($role)) {
            http_response_code(403);
            echo "Доступ запрещен: недостаточно прав";
            exit;
        }
    }
    
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }
}