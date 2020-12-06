<?php

namespace Core;

use Exception;
use PDO;
use PDOException;
use stdClass;

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
     * @var array
     */
    private array $bindValues = [];

    /**
     * @var int
     */
    private int $lastIDInserted;

    /**
     * @var stdClass
     */
    private stdClass $query;

    /**
     * DB constructor.
     * @throws Exception
     */
    private function __construct()
    {
    }

    /**
     * Connection to database
     *
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
     * Reset query
     */
    protected function reset(): void
    {
        $this->query = new stdClass();
        $this->bindValues = [];
    }

    /**
     * @param $table_name
     * @param  array  $fields
     * @return string
     * @throws Exception
     */
    public function insert($table_name, $fields = [])
    {
        $this->reset();
        $keys = implode(', ', array_keys($fields));
        $questionMarks = implode(', ', array_map(function ($value) {
            $this->bindValues[] = $value;
            return '?';
        }, array_values($fields)));

        $this->query->base = "INSERT INTO {$table_name} ({$keys}) VALUES ({$questionMarks})";
        $this->query->type = 'insert';
        return $this;
    }

    /**
     * @param $table_name
     * @param  array  $fields
     * @return $this
     */
    public function update($table_name, $fields = [])
    {
        $this->reset();
        $set = implode(", ", array_map(function ($key, $value) {
            $this->bindValues[] = $value;
            return "$key = ?";
        }, array_keys($fields), array_values($fields)));

        $this->query->base = "UPDATE {$table_name} SET $set";
        $this->query->type = 'update';
        return $this;
    }

    /**
     * @param $table_name
     * @return $this
     */
    public function delete($table_name)
    {
        $this->reset();
        $this->query->base = "DELETE FROM {$table_name}";
        $this->query->type = 'delete';

        return $this;
    }

    /**
     * @param  string  $table
     * @param  array  $fields
     * @return DB
     */
    public function select(string $table, array $fields)
    {
        $this->reset();
        $this->query->base = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @param  string  $operator
     * @return DB
     * @throws Exception
     */
    public function where(string $field, string $value, string $operator = '=')
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new Exception("WHERE can only be added to SELECT, UPDATE OR DELETE");
        }
        $this->query->where[] = "$field $operator ?";
        $this->bindValues[] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function lastId()
    {
        return $this->lastIDInserted;
    }

    /**
     * @return string
     */
    public function toSQL(): string
    {
        $query = $this->query;
        $sql = $this->query->base;

        if (!empty($this->query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        $sql .= ";";
        return $sql;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function exec()
    {
        try {
            $stmt = self::$connection->prepare($this->toSQL());
            $stmt->execute($this->bindValues);
            if($this->query->type === 'insert') {
                $this->lastIDInserted = self::$connection->lastInsertId();
            }
        } catch (PDOException $exception) {
            header("HTTP/1.0 500 Server Internal Error");
            throw new Exception($exception->getMessage());
        }
        return $stmt->rowCount();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function get()
    {
        try {
            $stmt = self::$connection->prepare($this->toSQL());
            $stmt->execute($this->bindValues);
        } catch (PDOException $exception) {
            header("HTTP/1.0 500 Server Internal Error");
            throw new Exception($exception->getMessage());
        }
        return $stmt->fetchAll();
    }
}