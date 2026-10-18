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
            self::createBanners($pdo, $db->isSqlite());
            self::createAnnouncements($pdo, $db->isSqlite());
            self::ensurePageMenuColumns($pdo, $db->isSqlite());
            self::ensureSeoColumns($pdo, $db->isSqlite());
            self::createAuditLogs($pdo, $db->isSqlite());
            self::seedAdmin($pdo);
            self::seedPages($pdo);
            self::seedPrivacyPolicy($pdo);
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
                `meta_title` VARCHAR(255) NOT NULL DEFAULT '',
                `meta_description` VARCHAR(320) NOT NULL DEFAULT '',
                `meta_keywords` VARCHAR(500) NOT NULL DEFAULT '',
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
                `show_in_header` TINYINT(1) NOT NULL DEFAULT 1,
                `show_in_footer` TINYINT(1) NOT NULL DEFAULT 1,
                `show_in_news` TINYINT(1) NOT NULL DEFAULT 0,
                `menu_order` INT NOT NULL DEFAULT 0,
                `meta_title` VARCHAR(255) NOT NULL DEFAULT '',
                `meta_description` VARCHAR(320) NOT NULL DEFAULT '',
                `meta_keywords` VARCHAR(500) NOT NULL DEFAULT '',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_pages_slug` (`slug`),
                INDEX `idx_pages_published` (`is_published`),
                INDEX `idx_pages_header` (`is_published`, `show_in_header`, `menu_order`),
                INDEX `idx_pages_footer` (`is_published`, `show_in_footer`, `menu_order`),
                INDEX `idx_pages_news` (`is_published`, `show_in_news`, `menu_order`)
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

    /**
     * Banners do carrossel da home. is_active=1 entra no site.
     */
    private static function createBanners(PDO $pdo, bool $sqlite): void
    {
        if ($sqlite) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS banners (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL DEFAULT '',
                    image_path TEXT NOT NULL,
                    link_url TEXT NOT NULL DEFAULT '',
                    sort_order INTEGER NOT NULL DEFAULT 0,
                    is_active INTEGER NOT NULL DEFAULT 1,
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
                )
            ");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_banners_active_order ON banners (is_active, sort_order)");
            return;
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `banners` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL DEFAULT '',
                `image_path` VARCHAR(255) NOT NULL,
                `link_url` VARCHAR(500) NOT NULL DEFAULT '',
                `sort_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_banners_active_order` (`is_active`, `sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Avisos em modal. Status: draft | published | hidden.
     * No máximo um "publicado" deve ser exibido (o mais recente).
     */
    private static function createAnnouncements(PDO $pdo, bool $sqlite): void
    {
        if ($sqlite) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS announcements (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL DEFAULT '',
                    body TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT 'draft',
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
                )
            ");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_announcements_status_updated ON announcements (status, updated_at)");
            return;
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `announcements` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL DEFAULT '',
                `body` TEXT NOT NULL,
                `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_announcements_status_updated` (`status`, `updated_at`)
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
                meta_title TEXT NOT NULL DEFAULT '',
                meta_description TEXT NOT NULL DEFAULT '',
                meta_keywords TEXT NOT NULL DEFAULT '',
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
                show_in_header INTEGER NOT NULL DEFAULT 1,
                show_in_footer INTEGER NOT NULL DEFAULT 1,
                show_in_news INTEGER NOT NULL DEFAULT 0,
                menu_order INTEGER NOT NULL DEFAULT 0,
                meta_title TEXT NOT NULL DEFAULT '',
                meta_description TEXT NOT NULL DEFAULT '',
                meta_keywords TEXT NOT NULL DEFAULT '',
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

        $stmt = $pdo->prepare('
            INSERT INTO pages (title, slug, content, is_published, show_in_header, show_in_footer, show_in_news, menu_order)
            VALUES (:title, :slug, :content, 1, 1, 1, 0, 0)
        ');
        foreach ($defaultPages as $page) {
            $stmt->execute([
                ':title' => $page['title'],
                ':slug' => $page['slug'],
                ':content' => $page['content'],
            ]);
        }
    }

    /**
     * Migra páginas existentes: colunas de posicionamento no menu.
     */
    private static function ensurePageMenuColumns(PDO $pdo, bool $sqlite): void
    {
        $columns = $sqlite
            ? [
                'show_in_header' => 'INTEGER NOT NULL DEFAULT 1',
                'show_in_footer' => 'INTEGER NOT NULL DEFAULT 1',
                'show_in_news' => 'INTEGER NOT NULL DEFAULT 0',
                'menu_order' => 'INTEGER NOT NULL DEFAULT 0',
            ]
            : [
                'show_in_header' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_in_footer' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_in_news' => 'TINYINT(1) NOT NULL DEFAULT 0',
                'menu_order' => 'INT NOT NULL DEFAULT 0',
            ];

        foreach ($columns as $name => $definition) {
            if (self::tableHasColumn($pdo, 'pages', $name, $sqlite)) {
                continue;
            }
            $pdo->exec("ALTER TABLE pages ADD COLUMN {$name} {$definition}");
        }

        if ($sqlite) {
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_pages_header ON pages (is_published, show_in_header, menu_order)');
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_pages_footer ON pages (is_published, show_in_footer, menu_order)');
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_pages_news ON pages (is_published, show_in_news, menu_order)');
        }
    }

    private static function tableHasColumn(PDO $pdo, string $table, string $column, bool $sqlite): bool
    {
        if ($sqlite) {
            $stmt = $pdo->query('PRAGMA table_info(' . $table . ')');
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            foreach ($rows as $row) {
                if (($row['name'] ?? '') === $column) {
                    return true;
                }
            }
            return false;
        }

        $stmt = $pdo->prepare('
            SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column
        ');
        $stmt->execute([':table' => $table, ':column' => $column]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private static function createAuditLogs(PDO $pdo, bool $sqlite): void
    {
        if ($sqlite) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS audit_logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NULL,
                    action TEXT NOT NULL,
                    table_name TEXT NOT NULL,
                    record_id INTEGER NULL,
                    details TEXT NOT NULL DEFAULT '',
                    ip_address TEXT NOT NULL DEFAULT '',
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_audit_created ON audit_logs (created_at)');
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_audit_table ON audit_logs (table_name, created_at)');
            return;
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `audit_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NULL,
                `action` VARCHAR(50) NOT NULL,
                `table_name` VARCHAR(64) NOT NULL,
                `record_id` INT NULL,
                `details` VARCHAR(500) NOT NULL DEFAULT '',
                `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_audit_created` (`created_at`),
                INDEX `idx_audit_table` (`table_name`, `created_at`),
                CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function seedPrivacyPolicy(PDO $pdo): void
    {
        $stmt = $pdo->prepare('SELECT id FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => 'politica-de-privacidade']);
        if ($stmt->fetch()) {
            return;
        }

        $content = '<h2>Política de Privacidade</h2>'
            . '<p>Esta Política de Privacidade descreve como a CONINFOMS trata dados pessoais em conformidade com a Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018).</p>'
            . '<h2>Cookies</h2>'
            . '<p>Utilizamos cookies necessários ao funcionamento do site e, mediante consentimento, cookies opcionais para preferências. Você pode aceitar todos ou gerenciar suas preferências no banner de cookies.</p>'
            . '<h2>Seus direitos</h2>'
            . '<p>Você pode solicitar acesso, correção, anonimização ou exclusão de dados pessoais pelos canais de contato disponibilizados neste site.</p>'
            . '<h2>Contato</h2>'
            . '<p>Em caso de dúvidas sobre privacidade, utilize o e-mail de contato institucional.</p>';

        $insert = $pdo->prepare('
            INSERT INTO pages (
                title, slug, content, is_published,
                show_in_header, show_in_footer, show_in_news, menu_order,
                meta_title, meta_description, meta_keywords
            ) VALUES (
                :title, :slug, :content, 1,
                0, 1, 0, 100,
                :meta_title, :meta_description, :meta_keywords
            )
        ');
        $insert->execute([
            ':title' => 'Política de Privacidade',
            ':slug' => 'politica-de-privacidade',
            ':content' => $content,
            ':meta_title' => 'Política de Privacidade | CONINFOMS',
            ':meta_description' => 'Saiba como a CONINFOMS trata dados pessoais e cookies conforme a LGPD.',
            ':meta_keywords' => 'lgpd, privacidade, cookies',
        ]);
    }

    /**
     * Meta Title / Description / Keywords em articles e pages.
     */
    private static function ensureSeoColumns(PDO $pdo, bool $sqlite): void
    {
        $defs = $sqlite
            ? [
                'meta_title' => "TEXT NOT NULL DEFAULT ''",
                'meta_description' => "TEXT NOT NULL DEFAULT ''",
                'meta_keywords' => "TEXT NOT NULL DEFAULT ''",
            ]
            : [
                'meta_title' => "VARCHAR(255) NOT NULL DEFAULT ''",
                'meta_description' => "VARCHAR(320) NOT NULL DEFAULT ''",
                'meta_keywords' => "VARCHAR(500) NOT NULL DEFAULT ''",
            ];

        foreach (['articles', 'pages'] as $table) {
            foreach ($defs as $name => $definition) {
                if (self::tableHasColumn($pdo, $table, $name, $sqlite)) {
                    continue;
                }
                $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$name} {$definition}");
            }
        }
    }
}
