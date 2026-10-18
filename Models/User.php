<?php

namespace Models;

use Core\Model;
use PDO;

class User extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT id, name, email, created_at FROM users ORDER BY name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public function create(array $data): bool
    {
        if ($this->findByEmail($data['email'])) {
            return false;
        }

        $stmt = $this->db->prepare('
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ');

        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare('
                UPDATE users
                SET name = :name, email = :email, password = :password
                WHERE id = :id
            ');
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);
        }

        $stmt = $this->db->prepare('
            UPDATE users
            SET name = :name, email = :email
            WHERE id = :id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':email' => $data['email'],
        ]);
    }

    public function delete(int $id): bool
    {
        if ($this->count() <= 1) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
