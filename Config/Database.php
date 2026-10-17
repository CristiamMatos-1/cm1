<?php

namespace Config;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    // Configurações do Banco de Dados
    private string $host = 'localhost';
    private string $db_name = 'coninfom_institucional';
    private string $username = 'coninfom_admin'; // Ajustar conforme o ambiente
    private string $password = 'SenhaConinfoms2026';     // Ajustar conforme o ambiente
    private string $charset = 'utf8mb4';

    /**
     * Construtor privado para impedir a instanciação direta (Padrão Singleton)
     */
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays associativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements reais
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Erro real do MySQL: " . $e->getMessage());
        }
    }

    /**
     * Previne a clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne a desserialização da instância
     */
    public function __wakeup()
    {
        throw new Exception("Não é possível desserializar um singleton.");
    }

    /**
     * Retorna a única instância da classe Database
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
