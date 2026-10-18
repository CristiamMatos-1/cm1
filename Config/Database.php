<?php

namespace Config;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexão PDO (Singleton). Credenciais vêm do ambiente, com fallback
 * para o cPanel atual — evita die() na home pública se o MySQL cair.
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private ?string $lastError = null;
    private string $driver = 'mysql';

    private function __construct()
    {
        $this->connect();
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new RuntimeException('Não é possível desserializar um singleton.');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function isConnected(): bool
    {
        return $this->connection instanceof PDO;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getConnection(): PDO
    {
        if (!$this->connection instanceof PDO) {
            throw new RuntimeException('Banco de dados indisponível: ' . ($this->lastError ?? 'conexão não estabelecida'));
        }
        return $this->connection;
    }

    public function isSqlite(): bool
    {
        return $this->driver === 'sqlite';
    }

    private function connect(): void
    {
        $driver = strtolower((string) App::env('DB_CONNECTION', 'mysql'));
        $this->driver = $driver === 'sqlite' ? 'sqlite' : 'mysql';

        try {
            if ($this->driver === 'sqlite') {
                $path = App::env('DB_DATABASE', App::rootPath() . '/storage/database.sqlite');
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $this->connection = new PDO('sqlite:' . $path, null, null, $this->pdoOptions());
                $this->connection->exec('PRAGMA foreign_keys = ON');
                return;
            }

            $host = App::env('DB_HOST', 'localhost');
            $name = App::env('DB_NAME', 'coninfom_institucional');
            $user = App::env('DB_USER', 'coninfom_admin');
            $pass = App::env('DB_PASS', 'SenhaConinfoms2026');
            $charset = App::env('DB_CHARSET', 'utf8mb4');

            $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
            $this->connection = new PDO($dsn, $user, $pass, $this->pdoOptions());
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->connection = null;
            error_log('Erro de conexão com o banco de dados: ' . $e->getMessage());
            $this->connectSqliteFallback();
        }
    }

    /**
     * Evita tela branca se o MySQL do cPanel estiver indisponível no ambiente local.
     */
    private function connectSqliteFallback(): void
    {
        try {
            $path = App::rootPath() . '/storage/database.sqlite';
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->driver = 'sqlite';
            $this->connection = new PDO('sqlite:' . $path, null, null, $this->pdoOptions());
            $this->connection->exec('PRAGMA foreign_keys = ON');
            error_log('Database: fallback SQLite ativado em ' . $path);
        } catch (PDOException $e) {
            $this->connection = null;
            $this->lastError = $e->getMessage();
        }
    }

    private function pdoOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    }
}
