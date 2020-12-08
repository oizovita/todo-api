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
            return json_encode(['Error' => 'Login must be unique', 'status' => 422]);
        }
        $data['pass'] = md5($data['pass']);
        $this->user->create($data);

        return json_encode(['status' => 201]);
    }

    /**
     * @return false|string
     * @throws Exception
     */
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $this->user->where(['login' => $data['login'], 'pass' => md5($data['pass'])]);
        if (empty($user)) {
            http_response_code(422);
            return json_encode(['Error' => 'Login not found', 'status' => 422]);
        }

        session_start();

        return json_encode(['status' => 200]);
    }

    /**
     *
     */
    public function logout()
    {
        session_start();
        setcookie("PHPSESSID", "", time(), "/", "." . '192.168.20.20');
        session_destroy();
        unset($_COOKIE["PHPSESSID"]);

        return json_encode(['status' => 200]);
    }
}