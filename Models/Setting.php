<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Setting extends Model
{
    public function get(string $keyName, string $default = ''): string
    {
        try {
            $stmt = $this->db->prepare('SELECT key_value FROM settings WHERE key_name = :key_name LIMIT 1');
            $stmt->bindValue(':key_name', $keyName, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result && $result['key_value'] !== null ? (string) $result['key_value'] : $default;
        } catch (Throwable $e) {
            return $default;
        }
    }

    public function set(string $keyName, string $keyValue): bool
    {
        if ($this->isSqlite()) {
            $stmt = $this->db->prepare('
                INSERT INTO settings (key_name, key_value)
                VALUES (:key_name, :key_value)
                ON CONFLICT(key_name) DO UPDATE SET key_value = :update_value
            ');
        } else {
            $stmt = $this->db->prepare('
                INSERT INTO settings (key_name, key_value)
                VALUES (:key_name, :key_value)
                ON DUPLICATE KEY UPDATE key_value = :update_value
            ');
        }

        $stmt->bindValue(':key_name', $keyName, PDO::PARAM_STR);
        $stmt->bindValue(':key_value', $keyValue, PDO::PARAM_STR);
        $stmt->bindValue(':update_value', $keyValue, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT key_name, key_value FROM settings');
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['key_name']] = $row['key_value'];
            }
            return $settings;
        } catch (Throwable $e) {
            return [];
        }
    }
}
