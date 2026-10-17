<?php

namespace Models;

use Config\Database;
use PDO;

class Page
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM pages ORDER BY created_at DESC");
            return $stmt ? $stmt->fetchAll() : [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getPublished(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM pages WHERE is_published = 1 ORDER BY title ASC");
            return $stmt ? $stmt->fetchAll() : [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = :slug AND is_published = 1");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO pages (title, slug, content, is_published) 
            VALUES (:title, :slug, :content, :is_published)
        ");
        
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $this->generateSlug($data['title']),
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE pages 
            SET title = :title, slug = :slug, content = :content, is_published = :is_published 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $this->generateSlug($data['title']),
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM pages WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function generateSlug(string $string): string
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $string);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        
        return $slug;
    }
}