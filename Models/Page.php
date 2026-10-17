<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Page extends Model
{
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM pages ORDER BY created_at DESC');
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPublished(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM pages WHERE is_published = 1 ORDER BY title ASC');
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE slug = :slug AND is_published = 1');
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO pages (title, slug, content, is_published)
            VALUES (:title, :slug, :content, :is_published)
        ');

        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $this->uniqueSlug($data['title']),
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $current = $this->findById($id);
        $slug = $current['slug'] ?? $this->uniqueSlug($data['title'], $id);
        if ($current && $current['title'] !== $data['title']) {
            $slug = $this->uniqueSlug($data['title'], $id);
        }

        $stmt = $this->db->prepare('
            UPDATE pages
            SET title = :title, slug = :slug, content = :content, is_published = :is_published
            WHERE id = :id
        ');

        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $slug,
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM pages WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = $this->slugify($title);
        $slug = $base;
        $i = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT id FROM pages WHERE slug = :slug';
        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        if ($ignoreId !== null) {
            $stmt->bindValue(':id', $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (bool) $stmt->fetch();
    }
}
