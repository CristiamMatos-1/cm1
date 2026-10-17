<?php

namespace Models;

use Config\Database;
use PDO;

class Setting
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Busca o valor de uma configuração pelo nome da chave
     */
    public function get(string $keyName, string $default = ''): string
    {
        try {
            $stmt = $this->db->prepare("SELECT key_value FROM settings WHERE key_name = :key_name LIMIT 1");
            $stmt->bindValue(':key_name', $keyName, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ? $result['key_value'] : $default;
        } catch (\PDOException $e) {
            return $default;
        }
    }

    /**
     * Salva ou atualiza o valor de uma configuração
     */
    public function set(string $keyName, string $keyValue): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO settings (key_name, key_value) 
            VALUES (:key_name, :key_value)
            ON DUPLICATE KEY UPDATE key_value = :update_value
        ");
        
        $stmt->bindValue(':key_name', $keyName, PDO::PARAM_STR);
        $stmt->bindValue(':key_value', $keyValue, PDO::PARAM_STR);
        $stmt->bindValue(':update_value', $keyValue, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Retorna todas as configurações como um array associativo
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT key_name, key_value FROM settings");
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['key_name']] = $row['key_value'];
        }
        
        return $settings;
    }
}
