<?php

namespace Src\Controllers;

use Src\Models\User;
use Exception;

/**
 * Class AuthController
 */
class AuthController
{
    /**
     * @var User
     */
    private User $user;

    /**
     * ItemController constructor.
     */
    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @return false|string
     * @throws Exception
     */
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($this->user->where(['login' => $data['login']]))) {
            http_response_code(422);
            return json_encode(['Error' => 'Login must be unique']);
        }
        $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);
        $response = $this->user->create($data);
        unset($response['pass']);
        return json_encode($response);
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($this->user->where(['login' => $data['login']]))) {
            http_response_code(422);
            return json_encode(['Error' => 'Login must be unique']);
        }

    }
}