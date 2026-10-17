<?php

namespace Models;

use Config\Database;
use PDO;

class Article
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Busca todos os artigos
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT a.*, u.name as author_name 
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            ORDER BY a.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca artigos publicados (Para o Frontend)
     */
    public function getPublished(int $limit = 10): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.name as author_name 
                FROM articles a 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.is_published = 1 
                ORDER BY a.created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Busca um artigo pelo ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca um artigo pelo Slug (Para o Frontend)
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as author_name 
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.slug = :slug AND a.is_published = 1
        ");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Cria um novo artigo
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO articles (title, slug, content, featured_image, is_published, author_id) 
            VALUES (:title, :slug, :content, :featured_image, :is_published, :author_id)
        ");
        
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $this->generateSlug($data['title']),
            ':content' => $data['content'],
            ':featured_image' => $data['featured_image'] ?? null,
            ':is_published' => $data['is_published'] ?? 0,
            ':author_id' => $data['author_id']
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um artigo existente
     */
    public function update(int $id, array $data): bool
    {
        $query = "UPDATE articles SET title = :title, slug = :slug, content = :content, is_published = :is_published";
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $this->generateSlug($data['title']),
            ':content' => $data['content'],
            ':is_published' => $data['is_published'] ?? 0
        ];

        // Atualiza a imagem apenas se uma nova foi enviada
        if (isset($data['featured_image'])) {
            $query .= ", featured_image = :featured_image";
            $params[':featured_image'] = $data['featured_image'];
        }

        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Exclui um artigo
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Gera um slug amigável para URL baseado no título
     */
    private function generateSlug(string $string): string
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $string);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        
        // Adiciona um uniqid curto para evitar colisão simples
        return $slug . '-' . substr(uniqid(), -4);
    }
}
