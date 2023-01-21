<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require __DIR__ . '/../vendor/autoload.php';

$dependencies = require_once "../dependencies/dependencies.php";



$app = new App\App($dependencies);


$app->run();