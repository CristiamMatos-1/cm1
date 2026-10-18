<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Announcement extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Rascunho',
        self::STATUS_PUBLISHED => 'Publicado',
        self::STATUS_HIDDEN => 'Oculto',
    ];

    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::STATUS_LABELS);
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM announcements ORDER BY updated_at DESC, id DESC');
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Aviso publicado mais recente (único exibido no modal).
     */
    public function getPublished(): ?array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT id, title, body, status, updated_at
                FROM announcements
                WHERE status = :status
                ORDER BY updated_at DESC, id DESC
                LIMIT 1
            ');
            $stmt->bindValue(':status', self::STATUS_PUBLISHED, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM announcements WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        if (($data['status'] ?? '') === self::STATUS_PUBLISHED) {
            $this->unpublishOthers(null);
        }

        $stmt = $this->db->prepare('
            INSERT INTO announcements (title, body, status)
            VALUES (:title, :body, :status)
        ');
        $stmt->execute([
            ':title' => $data['title'] ?? '',
            ':body' => $data['body'],
            ':status' => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (($data['status'] ?? '') === self::STATUS_PUBLISHED) {
            $this->unpublishOthers($id);
        }

        $stmt = $this->db->prepare('
            UPDATE announcements
            SET title = :title, body = :body, status = :status, updated_at = :updated_at
            WHERE id = :id
        ');
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'] ?? '',
            ':body' => $data['body'],
            ':status' => $data['status'],
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM announcements WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Garante no máximo um aviso Publicado (os demais viram Oculto).
     */
    private function unpublishOthers(?int $keepId): void
    {
        if ($keepId === null) {
            $stmt = $this->db->prepare('
                UPDATE announcements
                SET status = :hidden, updated_at = :updated_at
                WHERE status = :published
            ');
            $stmt->execute([
                ':hidden' => self::STATUS_HIDDEN,
                ':published' => self::STATUS_PUBLISHED,
                ':updated_at' => date('Y-m-d H:i:s'),
            ]);
            return;
        }

        $stmt = $this->db->prepare('
            UPDATE announcements
            SET status = :hidden, updated_at = :updated_at
            WHERE status = :published AND id != :id
        ');
        $stmt->execute([
            ':hidden' => self::STATUS_HIDDEN,
            ':published' => self::STATUS_PUBLISHED,
            ':id' => $keepId,
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
