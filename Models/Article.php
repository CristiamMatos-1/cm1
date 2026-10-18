<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Article extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query('
            SELECT a.*, u.name as author_name
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            ORDER BY a.created_at DESC
        ');
        return $stmt->fetchAll() ?: [];
    }

    public function countAll(): int
    {
        try {
            return (int) $this->db->query('SELECT COUNT(*) FROM articles')->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function getPublished(int $limit = 10): array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT a.*, u.name as author_name
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                WHERE a.is_published = 1
                ORDER BY a.created_at DESC
                LIMIT :limit
            ');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('
            SELECT a.*, u.name as author_name
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.slug = :slug AND a.is_published = 1
        ');
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO articles (
                title, slug, content, featured_image, is_published, author_id,
                meta_title, meta_description, meta_keywords
            ) VALUES (
                :title, :slug, :content, :featured_image, :is_published, :author_id,
                :meta_title, :meta_description, :meta_keywords
            )
        ');

        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $this->uniqueSlug($data['title']),
            ':content' => $data['content'],
            ':featured_image' => $data['featured_image'] ?? null,
            ':is_published' => $data['is_published'] ?? 0,
            ':author_id' => $data['author_id'],
            ':meta_title' => $data['meta_title'] ?? '',
            ':meta_description' => $data['meta_description'] ?? '',
            ':meta_keywords' => $data['meta_keywords'] ?? '',
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

        $query = 'UPDATE articles SET title = :title, slug = :slug, content = :content, is_published = :is_published,
            meta_title = :meta_title, meta_description = :meta_description, meta_keywords = :meta_keywords';
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $slug,
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 0,
            ':meta_title' => $data['meta_title'] ?? '',
            ':meta_description' => $data['meta_description'] ?? '',
            ':meta_keywords' => $data['meta_keywords'] ?? '',
        ];

        if (isset($data['featured_image'])) {
            $query .= ', featured_image = :featured_image';
            $params[':featured_image'] = $data['featured_image'];
        }

        $query .= ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = :id');
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
        $sql = 'SELECT id FROM articles WHERE slug = :slug';
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
