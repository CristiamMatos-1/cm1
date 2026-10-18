<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Page extends Model
{
    public const PLACE_HEADER = 'header';
    public const PLACE_FOOTER = 'footer';
    public const PLACE_NEWS = 'news';

    /** @var array<string, string> */
    private const PLACE_COLUMNS = [
        self::PLACE_HEADER => 'show_in_header',
        self::PLACE_FOOTER => 'show_in_footer',
        self::PLACE_NEWS => 'show_in_news',
    ];

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM pages ORDER BY menu_order ASC, title ASC');
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPublished(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM pages WHERE is_published = 1 ORDER BY menu_order ASC, title ASC');
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Páginas publicadas para um local de menu (header, footer ou news).
     */
    public function getPublishedFor(string $placement): array
    {
        $column = self::PLACE_COLUMNS[$placement] ?? null;
        if ($column === null) {
            return [];
        }

        try {
            $sql = "SELECT * FROM pages
                    WHERE is_published = 1 AND {$column} = 1
                    ORDER BY menu_order ASC, title ASC";
            $stmt = $this->db->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function placementLabels(): array
    {
        return [
            self::PLACE_HEADER => 'Menu Principal',
            self::PLACE_FOOTER => 'Rodapé',
            self::PLACE_NEWS => 'Últimas Novidades',
        ];
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
            INSERT INTO pages (
                title, slug, content, is_published,
                show_in_header, show_in_footer, show_in_news, menu_order,
                meta_title, meta_description, meta_keywords
            ) VALUES (
                :title, :slug, :content, :is_published,
                :show_in_header, :show_in_footer, :show_in_news, :menu_order,
                :meta_title, :meta_description, :meta_keywords
            )
        ');

        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $this->uniqueSlug($data['title']),
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1,
            ':show_in_header' => $data['show_in_header'] ?? 1,
            ':show_in_footer' => $data['show_in_footer'] ?? 1,
            ':show_in_news' => $data['show_in_news'] ?? 0,
            ':menu_order' => $data['menu_order'] ?? 0,
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

        $stmt = $this->db->prepare('
            UPDATE pages
            SET title = :title, slug = :slug, content = :content, is_published = :is_published,
                show_in_header = :show_in_header, show_in_footer = :show_in_footer,
                show_in_news = :show_in_news, menu_order = :menu_order,
                meta_title = :meta_title, meta_description = :meta_description,
                meta_keywords = :meta_keywords
            WHERE id = :id
        ');

        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $slug,
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 1,
            ':show_in_header' => $data['show_in_header'] ?? 1,
            ':show_in_footer' => $data['show_in_footer'] ?? 1,
            ':show_in_news' => $data['show_in_news'] ?? 0,
            ':menu_order' => $data['menu_order'] ?? 0,
            ':meta_title' => $data['meta_title'] ?? '',
            ':meta_description' => $data['meta_description'] ?? '',
            ':meta_keywords' => $data['meta_keywords'] ?? '',
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
