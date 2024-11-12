<?php

use App\HTTP\Router;

require_once '../vendor/autoload.php';

session_start();

// Register the Router and dispatch the action based on HTTP method and URI
$response = Router::register()->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

http_response_code($response->getHttpCode());

echo $response;