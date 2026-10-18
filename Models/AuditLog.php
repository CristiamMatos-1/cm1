<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class AuditLog extends Model
{
    public function latest(int $limit = 100): array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT a.*, u.name AS user_name, u.email AS user_email
                FROM audit_logs a
                LEFT JOIN users u ON u.id = a.user_id
                ORDER BY a.id DESC
                LIMIT :limit
            ');
            $stmt->bindValue(':limit', max(1, min(500, $limit)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function countAll(): int
    {
        try {
            return (int) $this->db->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }
}
