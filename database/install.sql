-- =============================================================================
-- CM1 / CONINFOMS — Instalação nova (MySQL / MariaDB)
-- =============================================================================
-- Uso (phpMyAdmin ou linha de comando):
--   1. Crie o banco vazio (utf8mb4), ex.: coninfom_institucional
--   2. Importe este arquivo no banco
--   3. Configure o .env na raiz do site (veja INSTALACAO.md)
--
-- Credencial inicial do painel (/login):
--   E-mail:    admin@coninfoms.com.br
--   Senha:     admin123
--   (altere imediatamente após o primeiro acesso)
-- =============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- -----------------------------------------------------------------------------
-- Limpeza (instalação limpa; comente se for atualizar um banco existente)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `downloads`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Tabelas
-- -----------------------------------------------------------------------------

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `articles` (
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

CREATE TABLE `pages` (
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

CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) NOT NULL UNIQUE,
    `key_value` TEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `downloads` (
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

CREATE TABLE `banners` (
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

CREATE TABLE `announcements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `body` TEXT NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_announcements_status_updated` (`status`, `updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
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

-- -----------------------------------------------------------------------------
-- Dados iniciais
-- -----------------------------------------------------------------------------

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Administrador', 'admin@coninfoms.com.br', '$2y$10$XWYaciUnB8Qqi6Nnt30iou40iiZMhEPX09LMirOb4keJP69DruI7a', NOW());

INSERT INTO `pages`
(`title`, `slug`, `content`, `is_published`, `show_in_header`, `show_in_footer`, `show_in_news`, `menu_order`, `meta_title`, `meta_description`, `meta_keywords`)
VALUES
(
    'Sobre a Empresa',
    'sobre-a-empresa',
    '<h2>Nossa história</h2><p>A CONINFOMS nasceu com o objetivo de simplificar a tecnologia para empresas de todos os tamanhos. Acreditamos que a inovação não deve ser um obstáculo, mas sim uma ponte para o crescimento sustentável.</p><h2>O que fazemos</h2><p>Entregamos desenvolvimento de sistemas, infraestrutura moderna e suporte contínuo, com foco em qualidade e resultados mensuráveis.</p>',
    1, 1, 1, 0, 10,
    'Sobre a Empresa | CONINFOMS',
    'Conheça a história, a missão e os serviços da CONINFOMS.',
    'coninfoms, sobre, empresa, tecnologia'
),
(
    'Tecnologias',
    'tecnologias',
    '<h2>Stack e competências</h2><p>Trabalhamos com tecnologias modernas de desenvolvimento web, bancos de dados, infraestrutura em nuvem e integração de sistemas, sempre alinhadas às necessidades do cliente.</p>',
    1, 1, 1, 0, 20,
    'Tecnologias | CONINFOMS',
    'Tecnologias e competências utilizadas pela CONINFOMS.',
    'tecnologias, desenvolvimento, infraestrutura'
),
(
    'Parceiros',
    'parceiros',
    '<h2>Parcerias</h2><p>Construímos relações de longo prazo com clientes e fornecedores. Em breve, destaques de parceiros estratégicos aparecerão nesta página.</p>',
    1, 1, 1, 0, 30,
    'Parceiros | CONINFOMS',
    'Parceiros e alianças estratégicas da CONINFOMS.',
    'parceiros, alianças'
),
(
    'Contato',
    'contato',
    '<h2>Fale conosco</h2><p>Entre em contato pelo e-mail institucional, telefone ou WhatsApp disponíveis no site. Nossa equipe retorna o mais breve possível.</p>',
    1, 1, 1, 0, 40,
    'Contato | CONINFOMS',
    'Canais de contato da CONINFOMS.',
    'contato, suporte, whatsapp'
),
(
    'Missão',
    'missao',
    '<h2>Nossa missão</h2><p>Levar inovação acessível e estável para o negócio do cliente, com transparência, qualidade e responsabilidade técnica.</p>',
    1, 1, 1, 0, 15,
    'Missão | CONINFOMS',
    'Missão e valores da CONINFOMS.',
    'missão, valores'
),
(
    'Política de Privacidade',
    'politica-de-privacidade',
    '<h2>Política de Privacidade</h2><p>Esta Política de Privacidade descreve como a CONINFOMS trata dados pessoais em conformidade com a Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018).</p><h2>Cookies</h2><p>Utilizamos cookies necessários ao funcionamento do site e, mediante consentimento, cookies opcionais para preferências. Você pode aceitar todos ou gerenciar suas preferências no banner de cookies.</p><h2>Seus direitos</h2><p>Você pode solicitar acesso, correção, anonimização ou exclusão de dados pessoais pelos canais de contato disponibilizados neste site.</p><h2>Contato</h2><p>Em caso de dúvidas sobre privacidade, utilize o e-mail de contato institucional.</p>',
    1, 0, 1, 0, 100,
    'Política de Privacidade | CONINFOMS',
    'Saiba como a CONINFOMS trata dados pessoais e cookies conforme a LGPD.',
    'lgpd, privacidade, cookies'
);

INSERT INTO `settings` (`key_name`, `key_value`) VALUES
('company_name', 'CONINFOMS'),
('company_logo', ''),
('whatsapp_number', ''),
('whatsapp_message', 'Olá! Gostaria de mais informações.'),
('contact_email', 'contato@coninfoms.com.br'),
('contact_phone', ''),
('hero_title', 'Inovação e Tecnologia para o seu Negócio'),
('hero_subtitle', 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.'),
('hero_btn1_text', 'Conheça a Empresa'),
('hero_btn1_link', '/cm1/pagina?slug=sobre-a-empresa'),
('hero_btn2_text', 'Nossos Serviços'),
('hero_btn2_link', '#servicos'),
('client_area_text', 'Área do Cliente'),
('client_area_link', 'https://coninfoms.com.br/cm/'),
('articles_section_title', 'Nossas Últimas Matérias'),
('news_section_title', ''),
('youtube_section_title', ''),
('youtube_section_subtitle', ''),
('footer_about_text', 'Soluções em tecnologia da informação. Trazendo inovação e estabilidade para o seu negócio.'),
('home_video_mode', 'none'),
('home_video_file', ''),
('youtube_api_key', ''),
('youtube_channel_id', ''),
('youtube_channel_url', ''),
('youtube_video_url', ''),
('social_instagram_url', ''),
('social_facebook_url', ''),
('social_linkedin_url', ''),
('social_youtube_url', ''),
('social_x_url', ''),
('social_tiktok_url', ''),
('social_whatsapp_url', ''),
('meta_access_token', ''),
('meta_page_id', ''),
('facebook_page_id', ''),
('facebook_access_token', ''),
('instagram_access_token', ''),
('instagram_account_id', ''),
('gemini_api_key', '');

-- Artigo de exemplo (publicado)
INSERT INTO `articles`
(`title`, `slug`, `content`, `featured_image`, `is_published`, `meta_title`, `meta_description`, `meta_keywords`, `author_id`, `created_at`, `updated_at`)
VALUES
(
    'Bem-vindo ao novo site institucional',
    'bem-vindo-ao-novo-site-institucional',
    '<p>Este é o primeiro artigo do portal CONINFOMS. Use o painel administrativo para publicar matérias, banners, avisos, páginas institucionais e arquivos de suporte.</p><p>O conteúdo deste site é gerenciado de forma dinâmica: altere textos, SEO, menus e mídias sem editar código.</p>',
    NULL,
    1,
    'Bem-vindo ao novo site institucional | CONINFOMS',
    'Conheça o portal institucional da CONINFOMS e as novidades do site.',
    'coninfoms, site, institucional, novidades',
    1,
    NOW(),
    NOW()
);

-- Fim
SELECT 'Instalação CM1 concluída. Login: admin@coninfoms.com.br / admin123' AS mensagem;
