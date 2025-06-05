<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    // Приватный конструктор (Singleton)
    private function __construct(array $config) {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        
        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? []
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    // Получение экземпляра
    public static function getInstance(array $config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                throw new Exception("Database configuration is required for first initialization");
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    // Выполнение запроса
    public function query(string $sql, array $params = []) {
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query execution error: " . $e->getMessage());
        }
    }
    
    // Получение одной записи
    public function fetch(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    // Получение всех записей
    public function fetchAll(string $sql, array $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    // Вставка записи
    public function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->query($sql, array_values($data));
        
        return $this->pdo->lastInsertId();
    }
    
    // Обновление записи
    public function update(string $table, array $data, array $conditions): int {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $where = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));
        
        $sql = "UPDATE $table SET $set WHERE $where";
        $params = array_merge(array_values($data), array_values($conditions));
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    // Удаление записей
    public function delete(string $table, array $conditions): int {
        $where = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($conditions)));
        
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, array_values($conditions));
        
        return $stmt->rowCount();
    }
    
    // Начало транзакции
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    // Подтверждение транзакции
    public function commit() {
        return $this->pdo->commit();
    }
    
    // Откат транзакции
    public function rollBack() {
        return $this->pdo->rollBack();
    }
}