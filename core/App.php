<?php

namespace Core;

use Exception;

/**
 * Class App
 */
class App
{
    private static $instance;

    private function __construct()
    {
    }


    private function __clone()
    {
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param  array  $config
     * @param  Router  $router
     * @return mixed
     * @throws Exception
     */
    public function handle(array $config, Router $router)
    {
        try {
            $result = $router->mapRequest();
            $controller = "Src\\Controllers\\$result[controller]";
            $action = $result['action'];
            if (isset($result['params'])) {
                return (new $controller)->$action($result['params']);
            }

            return (new $controller)->$action();
        } catch (Exception $exception) {
            echo json_encode(['Error' => $exception->getMessage()]);
        }
    }
}