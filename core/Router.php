<?php

namespace Core;

use Exception;

/**
 * Class Router
 */
class Router
{
    /**
     * @var mixed
     */
    private $routes;

    /**
     * Router constructor.
     * @param $file
     */
    public function __construct($file)
    {
        $this->routes = json_decode($file, true);
    }

    /**
     * Map request with route
     *
     * @return array
     * @throws Exception
     */
    public function mapRequest()
    {
        $result = [];
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parsed_url['path'])) {
            $path = $parsed_url['path'];
        } else {
            $path = '/';
        }
        $routeNotFound = true;
        $methodNotAllowed = true;
        foreach ($this->routes as $route) {
            if (preg_match("#^$route[path]$#", $path, $matches)) {
                $routeNotFound = false;
                if ($route['method'] === $_SERVER['REQUEST_METHOD']) {
                    $methodNotAllowed = false;
                    array_shift($matches);
                    $result['controller'] = $route['controller'];
                    $result['action'] = $route['action'];
                    if (!empty($matches)) {
                        $result['params'] = array_shift($matches);
                    }
                    break;
                }
            }
        }

        if ($routeNotFound) {
            header("HTTP/1.0 404 Not Found");
            throw new Exception('Not Found');
        }

        if ($methodNotAllowed) {
            header("HTTP/1.0 405 Method Not Allowed");
            throw new Exception('Method Not Allowed');
        }

        return $result;
    }
}