<?php
require_once __DIR__ . '/../vendor/autoload.php';
$configs = include('../config.php');

use Core\Router;
use Core\App;

header("Access-Control-Allow-Origin: http://localhost:9090");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header('Access-Control-Allow-Credentials: true');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: Overwrite, Destination, Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control');
    header('Access-Control-Max-Age: 86400');
    exit(0);
}

header("Content-type: application/json; charset=utf-8");

$router = new Router(file_get_contents('../routes.json'));

$app = App::getInstance();

echo $app->handle($configs, $router);




