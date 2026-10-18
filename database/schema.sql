-- Schema MySQL do CMS institucional (devoluto.com.br/cm1)
-- Prepared statements / PDO são usados em todas as queries da aplicação.

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) NOT NULL UNIQUE,
    `key_value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Arquivos de suporte remoto (status: published | hidden | removed)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Carrossel da home (só is_active=1 aparece)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Avisos em modal (status: draft | published | hidden)
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `body` TEXT NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_announcements_status_updated` (`status`, `updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auditoria do painel (criar/editar/excluir/status)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
