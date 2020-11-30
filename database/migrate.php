<?php
require __DIR__ . '/../vendor/autoload.php';

use Core\DB;

$config = include(__DIR__ . '/../config.php');

$db = DB::connection(
    $config['driver'],
    $config['port'],
    $config['host'],
    $config['database'],
    $config['username'],
    $config['password']
);

const DIR = __DIR__ . '/migrations';

$files = scandir(DIR);

foreach ($files as $file) {
    if (!($file === '.' || $file === '..')) {
        $db->sql(file_get_contents(__DIR__ . "/migrations/$file"));
    }
}

