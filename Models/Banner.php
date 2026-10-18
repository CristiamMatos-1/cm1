<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Banner extends Model
{
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM banners ORDER BY sort_order ASC, id DESC');
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getActive(): array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT id, title, image_path, link_url, sort_order
                FROM banners
                WHERE is_active = 1
                ORDER BY sort_order ASC, id DESC
            ');
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM banners WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO banners (title, image_path, link_url, sort_order, is_active)
            VALUES (:title, :image_path, :link_url, :sort_order, :is_active)
        ');
        $stmt->execute([
            ':title' => $data['title'],
            ':image_path' => $data['image_path'],
            ':link_url' => $data['link_url'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':is_active' => $data['is_active'] ?? 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [
            'title = :title',
            'link_url = :link_url',
            'sort_order = :sort_order',
            'is_active = :is_active',
            'updated_at = :updated_at',
        ];
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':link_url' => $data['link_url'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':is_active' => $data['is_active'] ?? 0,
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($data['image_path'])) {
            $fields[] = 'image_path = :image_path';
            $params[':image_path'] = $data['image_path'];
        }

        $sql = 'UPDATE banners SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM banners WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
