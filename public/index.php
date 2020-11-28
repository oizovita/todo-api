<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\App;

header("Content-type: application/json; charset=utf-8");

$router = new Router(file_get_contents('../routes.json'));

$app = App::getInstance();

$app->handle([], $router);




