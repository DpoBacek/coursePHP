<?php
class IncomingService {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function getMonthTotal(): int {
        $startOfMonth = date('Y-m-01');
        return $this->db->fetchColumn("
            SELECT SUM(quantity) 
            FROM incoming 
            WHERE received_at >= ?
        ", [$startOfMonth]);
    }
    
    public function getUpcomingIncoming(int $limit): array {
        return $this->db->fetchAll("
            SELECT s.name AS supplier_name, p.name AS product_name, i.quantity, i.expected_date
            FROM incoming i
            JOIN suppliers s ON i.supplier_id = s.id
            JOIN products p ON i.product_id = p.id
            WHERE i.expected_date >= CURDATE()
            ORDER BY i.expected_date ASC
            LIMIT ?
        ", [$limit]);
    }
}
?>