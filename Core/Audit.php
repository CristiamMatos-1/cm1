<?php

namespace Core;

use Config\Database;
use PDO;
use Throwable;

/**
 * Log de auditoria: quem, ação, tabela, timestamp, IP.
 */
class Audit
{
    public static function log(string $action, string $tableName, ?int $recordId = null, string $details = ''): void
    {
        try {
            $db = Database::getInstance();
            if (!$db->isConnected()) {
                return;
            }

            $pdo = $db->getConnection();
            $stmt = $pdo->prepare('
                INSERT INTO audit_logs (user_id, action, table_name, record_id, details, ip_address, created_at)
                VALUES (:user_id, :action, :table_name, :record_id, :details, :ip_address, :created_at)
            ');
            $stmt->execute([
                ':user_id' => Auth::id(),
                ':action' => mb_substr(trim($action), 0, 50),
                ':table_name' => mb_substr(trim($tableName), 0, 64),
                ':record_id' => $recordId,
                ':details' => mb_substr(trim($details), 0, 500),
                ':ip_address' => mb_substr(self::clientIp(), 0, 45),
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            error_log('Audit::log falhou: ' . $e->getMessage());
        }
    }

    public static function clientIp(): string
    {
        $candidates = [
            $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
            $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ];
        foreach ($candidates as $value) {
            if (!is_string($value) || trim($value) === '') {
                continue;
            }
            $first = trim(explode(',', $value)[0]);
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        return '0.0.0.0';
    }
}
