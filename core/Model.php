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
        $this->db->delete($this->getTable())->where('id', $id)->exec();
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
        $this->db->insert($this->getTable(), $fields)->exec();
        return $this->db->select($this->getTable(), ['*'])->where('id', $this->db->lastId())->get();
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
        $baseSql = $this->db->select($this->getTable(), ['*']);
        foreach ($fields as $field => $value) {
            $baseSql->where($field, $value);
        }

        return $baseSql->get();
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
        $this->db->update($this->getTable(), $fields)->where('id', $id)->exec();
        return $this->db->select($this->getTable(), ['*'])->where('id', $id)->get();
    }

    /**
     * Get all entries
     *
     * @return array
     * @throws Exception
     */
    public function get()
    {
        return $this->db->select($this->getTable(), ['*'])->get();
    }
}