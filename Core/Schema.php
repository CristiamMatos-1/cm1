<?php

namespace Core;

use Config\App;
use Config\Database;
use PDO;
use Throwable;

/**
 * Garante o schema mínimo (users, articles, pages, settings) e o admin inicial.
 * Extraído do AdminPageController — schema não é responsabilidade de um CRUD.
 */
class Schema
{
    public static function ensure(): void
    {
        $db = Database::getInstance();
        if (!$db->isConnected()) {
            return;
        }

        try {
            $pdo = $db->getConnection();
            if ($db->isSqlite()) {
                self::createSqlite($pdo);
            } else {
                self::createMysql($pdo);
            }
            self::createDownloads($pdo, $db->isSqlite());
            self::seedAdmin($pdo);
            self::seedPages($pdo);
        } catch (Throwable $e) {
            error_log('Schema::ensure falhou: ' . $e->getMessage());
        }
    }

    private static function createMysql(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `articles` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `content` MEDIUMTEXT NOT NULL,
                `featured_image` VARCHAR(255) NULL,
                `is_published` TINYINT(1) NOT NULL DEFAULT 0,
                `author_id` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_articles_slug` (`slug`),
                INDEX `idx_articles_published_created` (`is_published`, `created_at`),
                CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `pages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `content` MEDIUMTEXT NOT NULL,
                `is_published` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_pages_slug` (`slug`),
                INDEX `idx_pages_published` (`is_published`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `key_name` VARCHAR(100) NOT NULL UNIQUE,
                `key_value` TEXT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_settings_key` (`key_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Arquivos de suporte remoto. Status: published | hidden | removed.
     */
    private static function createDownloads(PDO $pdo, bool $sqlite): void
    {
        if ($sqlite) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS downloads (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    display_name TEXT NOT NULL,
                    stored_name TEXT NOT NULL UNIQUE,
                    original_name TEXT NOT NULL,
                    mime_type TEXT NOT NULL,
                    file_size INTEGER NOT NULL DEFAULT 0,
                    status TEXT NOT NULL DEFAULT 'published',
                    sort_order INTEGER NOT NULL DEFAULT 0,
                    created_by INTEGER NULL,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_downloads_status_order ON downloads (status, sort_order)");
            return;
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `downloads` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `display_name` VARCHAR(255) NOT NULL,
                `stored_name` VARCHAR(255) NOT NULL UNIQUE,
                `original_name` VARCHAR(255) NOT NULL,
                `mime_type` VARCHAR(127) NOT NULL,
                `file_size` INT UNSIGNED NOT NULL DEFAULT 0,
                `status` VARCHAR(20) NOT NULL DEFAULT 'published',
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_by` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_downloads_status_order` (`status`, `sort_order`),
                CONSTRAINT `fk_downloads_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function createSqlite(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS articles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT NOT NULL,
                featured_image TEXT NULL,
                is_published INTEGER NOT NULL DEFAULT 0,
                author_id INTEGER NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_articles_slug ON articles (slug)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_articles_published_created ON articles (is_published, created_at)");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                content TEXT NOT NULL,
                is_published INTEGER NOT NULL DEFAULT 1,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_pages_slug ON pages (slug)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_pages_published ON pages (is_published)");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key_name TEXT NOT NULL UNIQUE,
                key_value TEXT NULL,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private static function seedAdmin(PDO $pdo): void
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $email = App::env('ADMIN_EMAIL', 'admin@coninfoms.com.br');
        $password = App::env('ADMIN_PASSWORD', 'admin123');

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $stmt->execute([
            ':name' => 'Administrador',
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    private static function seedPages(PDO $pdo): void
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $defaultPages = [
            ['title' => 'Sobre a Empresa', 'slug' => 'sobre-a-empresa', 'content' => '<p>Conteúdo da página Sobre a Empresa.</p>'],
            ['title' => 'Tecnologias', 'slug' => 'tecnologias', 'content' => '<p>Conteúdo da página Tecnologias.</p>'],
            ['title' => 'Parceiros', 'slug' => 'parceiros', 'content' => '<p>Conteúdo da página Parceiros.</p>'],
            ['title' => 'Contato', 'slug' => 'contato', 'content' => '<p>Conteúdo da página Contato.</p>'],
        ];

        $stmt = $pdo->prepare('INSERT INTO pages (title, slug, content, is_published) VALUES (:title, :slug, :content, 1)');
        foreach ($defaultPages as $page) {
            $stmt->execute([
                ':title' => $page['title'],
                ':slug' => $page['slug'],
                ':content' => $page['content'],
            ]);
        }
    }
}
