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
    /**
     * @var string
     */
    protected string $table;

    /**
     * @var DB
     */
    private DB $db;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->db = App::getInstance()->db;
    }

    /**
     * Delete entry by id
     *
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
     * Create new entry
     *
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
        return $this->where(['id' => $id]);
    }

    /**
     * Get one entry by id
     *
     * @param  array  $fields
     * @return array
     * @throws Exception
     */
    public function where(array $fields)
    {
        $column = array_keys($fields)[0];
        $value = array_values($fields)[0];
        return $this->db->sql("SELECT * FROM {$this->getTable()} WHERE {$column} = ? ", [$value])->fetch();
    }

    /**
     * Update entry by id
     *
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

        return $this->where(['id' => $id]);
    }

    /**
     * Get all entries
     *
     * @return array
     * @throws Exception
     */
    public function get()
    {
        return $this->db->sql("SELECT * FROM {$this->getTable()}")->fetchAll();
    }
}