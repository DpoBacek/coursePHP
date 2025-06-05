<?php
class MovementRepository {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function getLast30DaysMovement(): array {
        return $this->db->fetchAll("
            SELECT 
                DATE(received_at) AS date,
                SUM(CASE WHEN type = 'incoming' THEN quantity ELSE 0 END) AS incoming,
                SUM(CASE WHEN type = 'outgoing' THEN quantity ELSE 0 END) AS outgoing
            FROM (
                SELECT received_at AS date, quantity, 'incoming' AS type FROM incoming
                UNION ALL
                SELECT sent_at AS date, quantity, 'outgoing' AS type FROM outgoing
            ) movements
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY date
            ORDER BY date ASC
        ");
    }
}
?>