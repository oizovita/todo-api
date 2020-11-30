<?php

namespace Core;

use Exception;

/**
 * Class App
 */
final class App
{
    private static $instance;

    public $db;

    private function __construct()
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
            $this->db = DB::connection(
                $config['driver'],
                $config['port'],
                $config['host'],
                $config['database'],
                $config['username'],
                $config['password']
            );

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

    private function __clone()
    {
    }
}