<?php

namespace Src\Controllers;

use Src\Models\Item;

/**
 * Class ItemController
 */
class ItemController
{
    /**
     * @var Item
     */
    private Item $item;

    public function __construct()
    {
        $this->item = new Item();
    }

    public function index()
    {
        return json_encode($this->item->get());
    }

    public function show($id)
    {
       return json_encode($this->item->where($id));
    }

    public function create()
    {
       return json_encode($this->item->create($_POST));
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"),$post_vars);

       return json_encode($this->item->update($id, $post_vars));
    }

    public function delete($id)
    {
        $this->item->delete($id);

        http_response_code(204);
    }
}