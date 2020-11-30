<?php

namespace Core;

use Exception;

/**
 *
 * Class Model
 * @package Core
 */
class Model
{
    protected string $table;
    private DB $db;

    public function __construct()
    {
        $this->db = App::getInstance()->db;
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function delete($id)
    {
        return $this->db->sql("DELETE FROM {$this->getTable()} WHERE id = ?", [$id]);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? strtolower(basename(str_replace('\\', '/', get_class($this)))) . 's';
    }

    /**
     * @param $fields
     * @return mixed
     * @throws Exception
     */
    public function create(array $fields)
    {
        $keys = array_keys($fields);
        $namedPlaceholder = implode(", ", array_map(function ($value) {
            return ":$value";
        }, $keys));

        $keys = implode(", ", $keys);
        $this->db->sql("INSERT INTO {$this->getTable()} ($keys) VALUES ($namedPlaceholder)", $fields);
        $id = $this->db->getConnection()->lastInsertId();
        return $this->where($id);
    }

    /**
     * @param $id
     * @param $fields
     * @return mixed
     * @throws Exception
     */
    public function update($id, array $fields)
    {
        $keys = implode(", ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($fields)));

        $this->db->sql("UPDATE {$this->getTable()} SET $keys  WHERE id = ?",
            array_merge(array_values($fields), [$id]));

        return $this->where($id);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function get()
    {
        return $this->db->sql("SELECT * FROM {$this->getTable()}")->fetchAll();
    }

    /**
     * @param $id
     * @return array
     * @throws Exception
     */
    public function where($id)
    {
        return $this->db->sql("SELECT * FROM {$this->getTable()} WHERE id = ? ", [$id])->fetch();
    }
}