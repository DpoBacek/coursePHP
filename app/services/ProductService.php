<?php
class ProductService {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function getTotalCount(): int {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM products");
    }
    
    public function getLowStockCount(): int {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE quantity <= min_quantity");
    }
    
    public function getLowStockProducts(int $limit): array {
        return $this->db->fetchAll("
            SELECT p.id, p.name, l.name AS location_name, p.quantity, p.min_quantity
            FROM products p
            JOIN locations l ON p.location_id = l.id
            WHERE p.quantity <= p.min_quantity
            ORDER BY p.quantity ASC
            LIMIT ?
        ", [$limit]);
    }
}
?>