<?php
include_once '../vendor/autoload.php';

use Starryseer\Work\Foundation\Application;
$app = new Application(__DIR__.'/..');
$app->run();