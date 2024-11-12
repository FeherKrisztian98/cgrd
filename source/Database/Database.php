<?php

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;

/**
 * This class provides a singleton instance for interacting with a MySQL database using PDO
 */
final class Database
{
    /** @var Database Singleton instance of the Database class */
    protected static self $instance;

    /** @var PDO PDO instance for database interaction */
    protected PDO $pdo;


    /**
     * Private constructor to prevent direct instantiation. establishes a connection to the database using environment variables
     *
     * @throws PDOException If the database connection fails
     *
     */
    private function __construct()
    {
        $host = getenv('DB_HOST');
        $dbName = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        if (!$host || !$dbName || !$username || !$password) {
            throw new PDOException('Database configuration is missing.');
        }

        if (!isset($this->pdo)) {
            $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
            $this->pdo = new PDO($dsn, $username, $password);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Gets the singleton instance of the Database class
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Executes an SQL query and returns the result as a PDOStatement
     *
     * @param string $query The SQL query to execute
     *
     * @return false|PDOStatement The PDOStatement on success, or false on failure
     */
    public function query(string $query): false|PDOStatement
    {
        return $this->pdo->query($query);
    }

    /**
     * Prepares an SQL statement for execution
     *
     * @param string $query The SQL query to prepare
     * @param array $options Optional array of options to pass to the prepare method
     * @return false|PDOStatement The prepared statement or false on failure
     */

    public function prepare(string $query, array $options = []): false|PDOStatement
    {
        return $this->pdo->prepare($query, $options);
    }

    /**
     * Gets the last inserted ID from the database
     *
     * @return false|string The last inserted ID, or false if no ID is available
     */
    public function lastInsertId(): false|string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Start a transaction
     *
     * @return void
     */
    public function startTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Roll back the transaction in progress
     *
     * @return void
     */
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }
}