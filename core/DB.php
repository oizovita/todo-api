<?php

namespace Core;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class DB
 * @package Core
 */
final class DB
{
    /**
     * @var null
     */
    protected static $instance = null;
    /**
     * @var PDO
     */
    private static PDO $connection;

    /**
     * DB constructor.
     * @throws Exception
     */
    private function __construct()
    {
    }

    /**
     * @param $driver
     * @param $port
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @return null
     * @throws Exception
     */
    public static function connection($driver, $port, $host, $database, $username, $password)
    {
        if (static::$instance === null) {
            try {
                self::$connection = new PDO(
                    "$driver:host=$host;port=$port;dbname=$database;charset=utf8",
                    $username,
                    $password,
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                static::$instance = new static();
            } catch (PDOException $e) {
                throw new Exception ($e->getMessage());
            }
        }

        return static::$instance;
    }

    /**
     * @param $query
     * @param  array  $args
     * @return mixed
     * @throws Exception
     */
    public function sql($query, $args = [])
    {
        return $this->run($query, $args);
    }

    /**
     * @param $query
     * @param  array  $args
     * @return PDOStatement
     * @throws Exception
     */
    private function run($query, $args = [])
    {
        try {
            if (!$args) {
                return self::$connection->query($query);
            }

            $stmt = self::$connection->prepare($query);
            $stmt->execute($args);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get connection
     * @return PDO
     */
    public function getConnection()
    {
        return self::$connection;
    }
}