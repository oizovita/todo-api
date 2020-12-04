<?php

namespace Src\Controllers;

use Src\Models\Item;
use Exception;

/**
 * Class ItemController
 */
class ItemController
{
    /**
     * @var Item
     */
    private Item $item;

    /**
     * ItemController constructor.
     */
    public function __construct()
    {
        $this->item = new Item();
    }

    /**
     * Get all items
     *
     * @return false|string
     * @throws Exception
     */
    public function index()
    {
        return json_encode(['data' => $this->item->get()]);
    }

    /**
     * Get one item by id
     *
     * @param $id
     * @return false|string
     * @throws Exception
     */
    public function show($id)
    {
        return json_encode($this->item->where(['id' => $id]));
    }

    /**
     * Create new item
     *
     * @return false|string
     * @throws Exception
     */
    public function create()
    {
        return json_encode($this->item->create(json_decode(file_get_contents('php://input'), true)));
    }

    /**
     * Update item by id
     *
     * @param $id
     * @return false|string
     * @throws Exception
     */
    public function update($id)
    {
        return json_encode($this->item->update($id, json_decode(file_get_contents('php://input'), true)));
    }

    /**
     * Delete item by id
     *
     * @param $id
     * @throws Exception
     */
    public function delete($id)
    {
        $this->item->delete($id);

        http_response_code(204);
    }
}