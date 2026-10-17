<?php

namespace Models;

use Config\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Busca todos os usuários
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT id, name, email, created_at FROM users ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Busca um usuário pelo ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Busca um usuário pelo email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Verifica as credenciais do usuário
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove a senha do array antes de retornar para segurança
            unset($user['password']);
            return $user;
        }
        
        return null;
    }

    /**
     * Cria um novo usuário
     */
    public function create(array $data): bool
    {
        // Verifica se o email já existe
        if ($this->findByEmail($data['email'])) {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password) 
            VALUES (:name, :email, :password)
        ");
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Atualiza os dados de um usuário
     */
    public function update(int $id, array $data): bool
    {
        // Se a senha foi preenchida, atualiza com a senha nova
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = :name, email = :email, password = :password 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT)
            ]);
        } else {
            // Se a senha estiver vazia, atualiza apenas nome e email
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = :name, email = :email 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':email' => $data['email']
            ]);
        }
    }

    /**
     * Exclui um usuário
     */
    public function delete(int $id): bool
    {
        // Previne que o único/último usuário seja deletado (opcional, mas recomendado)
        $countStmt = $this->db->query("SELECT COUNT(*) FROM users");
        $count = $countStmt->fetchColumn();
        
        if ($count <= 1) {
            return false; // Não pode deletar o último usuário
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
