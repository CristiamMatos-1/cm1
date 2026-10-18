<?php

namespace Models;

use Core\Model;
use PDO;
use Throwable;

class Download extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';
    public const STATUS_REMOVED = 'removed';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_PUBLISHED => 'Publicado',
        self::STATUS_HIDDEN => 'Oculto',
        self::STATUS_REMOVED => 'Removido',
    ];

    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::STATUS_LABELS);
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('
                SELECT d.*, u.name AS uploader_name
                FROM downloads d
                LEFT JOIN users u ON d.created_by = u.id
                ORDER BY d.sort_order ASC, d.created_at DESC
            ');
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPublished(): array
    {
        try {
            $stmt = $this->db->prepare('
                SELECT id, display_name, original_name, file_size, mime_type, created_at
                FROM downloads
                WHERE status = :status
                ORDER BY sort_order ASC, created_at DESC
            ');
            $stmt->bindValue(':status', self::STATUS_PUBLISHED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM downloads WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findPublishedById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM downloads WHERE id = :id AND status = :status');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', self::STATUS_PUBLISHED, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function countAll(): int
    {
        try {
            return (int) $this->db->query('SELECT COUNT(*) FROM downloads')->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO downloads (
                display_name, stored_name, original_name, mime_type, file_size, status, sort_order, created_by
            ) VALUES (
                :display_name, :stored_name, :original_name, :mime_type, :file_size, :status, :sort_order, :created_by
            )
        ');
        $stmt->execute([
            ':display_name' => $data['display_name'],
            ':stored_name' => $data['stored_name'],
            ':original_name' => $data['original_name'],
            ':mime_type' => $data['mime_type'],
            ':file_size' => $data['file_size'],
            ':status' => $data['status'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':created_by' => $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [
            'display_name = :display_name',
            'status = :status',
            'sort_order = :sort_order',
            'updated_at = :updated_at',
        ];
        $params = [
            ':id' => $id,
            ':display_name' => $data['display_name'],
            ':status' => $data['status'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':updated_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($data['stored_name'])) {
            $fields[] = 'stored_name = :stored_name';
            $fields[] = 'original_name = :original_name';
            $fields[] = 'mime_type = :mime_type';
            $fields[] = 'file_size = :file_size';
            $params[':stored_name'] = $data['stored_name'];
            $params[':original_name'] = $data['original_name'];
            $params[':mime_type'] = $data['mime_type'];
            $params[':file_size'] = $data['file_size'];
        }

        $sql = 'UPDATE downloads SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function setStatus(int $id, string $status): bool
    {
        if (!self::isValidStatus($status)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE downloads SET status = :status, updated_at = :updated_at WHERE id = :id');
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
