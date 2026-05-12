<?php

/**
 * Database Configuration File
 * 
 * Contains database connection parameters and connection helper methods
 */

namespace Config;

class Database
{
    private static string $DB_HOST = 'localhost';
    private static string $DB_USER = 'root';
    private static string $DB_PASSWORD = '';
    private static string $DB_NAME = 'f1_fantasy_manager';
    private static int $DB_PORT = 3306;

    private static ?\mysqli $connection = null;

    /**
     * Get MySQLi connection instance
     * 
     * @return \mysqli
     * @throws \Exception
     */
    public static function getConnection(): \mysqli
    {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$connection;
    }

    /**
     * Establish database connection
     * 
     * @throws \Exception
     */
    private static function connect(): void
    {
        self::$connection = new \mysqli(
            self::$DB_HOST,
            self::$DB_USER,
            self::$DB_PASSWORD,
            self::$DB_NAME,
            self::$DB_PORT
        );

        if (self::$connection->connect_error) {
            throw new \Exception('Database connection failed: ' . self::$connection->connect_error);
        }

        // Set charset
        self::$connection->set_charset('utf8mb4');
    }

    /**
     * Close database connection
     */
    public static function closeConnection(): void
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }

    /**
     * Execute a query
     * 
     * @param string $sql
     * @return \mysqli_result|bool
     * @throws \Exception
     */
    public static function query(string $sql)
    {
        try {
            $conn = self::getConnection();
            $result = $conn->query($sql);
            
            if ($result === false) {
                throw new \Exception('Query error: ' . $conn->error);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Execute a prepared statement
     * 
     * @param string $sql
     * @param string $types
     * @param mixed ...$params
     * @return \mysqli_stmt
     * @throws \Exception
     */
    public static function prepare(string $sql, string $types = '', ...$params): \mysqli_stmt
    {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                throw new \Exception('Prepare error: ' . $conn->error);
            }
            
            if (!empty($params) && !empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            
            return $stmt;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the last inserted ID
     * 
     * @return int
     */
    public static function getLastInsertId(): int
    {
        return self::getConnection()->insert_id;
    }

    /**
     * Get the number of affected rows
     * 
     * @return int
     */
    public static function getAffectedRows(): int
    {
        return self::getConnection()->affected_rows;
    }

    /**
     * Escape string for database queries
     * 
     * @param string $string
     * @return string
     */
    public static function escape(string $string): string
    {
        return self::getConnection()->real_escape_string($string);
    }

    /**
     * Set custom database credentials (for testing or different environments)
     * 
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $name
     * @param int $port
     */
    public static function setCredentials(string $host, string $user, string $password, string $name, int $port = 3306): void
    {
        self::$DB_HOST = $host;
        self::$DB_USER = $user;
        self::$DB_PASSWORD = $password;
        self::$DB_NAME = $name;
        self::$DB_PORT = $port;
        self::$connection = null;
    }
}

?>
