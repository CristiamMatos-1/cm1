<?php

namespace Core;

use Config\Database;
use PDO;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function isSqlite(): bool
    {
        return Database::getInstance()->isSqlite();
    }

    /**
     * Gera slug estável (sem uniqid). Unicidade é garantida pelo caller.
     */
    protected function slugify(string $string): string
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $string) ?? '';
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if ($converted !== false) {
            $slug = $converted;
        }
        $slug = preg_replace('~[^-\w]+~', '', $slug) ?? '';
        $slug = trim($slug, '-');
        $slug = strtolower((string) preg_replace('~-+~', '-', $slug));

        return $slug !== '' ? $slug : 'item';
    }
}
