<?php
class AuthService {
    private $db;
    private $logger;

    public function __construct(Database $db, Logger $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function attemptLogin(array $credentials) {
        $user = $this->db->query(
            "SELECT id, username, password, role FROM users WHERE username = ?",
            [$credentials['username']]
        )->fetch();

        if ($user && password_verify($credentials['password'], $user['password'])) {
            unset($user['password']);
            $this->logger->info("User {$user['id']} logged in");
            return $user;
        }
        
        $this->logger->warning("Failed login attempt for {$credentials['username']}");
        return null;
    }

    public function logout() {
        session_destroy();
    }
}
?>