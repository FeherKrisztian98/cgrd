<?php

use App\HTTP\Router;
use App\Service\Environment;

require_once '../vendor/autoload.php';

// Start output buffering to keep headers being sent first
ob_start();
Environment::bootstrap();

// Initialize Router and dispatch request
$response = Router::register()->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// Set the appropriate HTTP status code based on the response
http_response_code($response->getHttpCode());

// Send handler response only if no error occurred
if (ob_get_length() === 0) {
    echo $response;
}
ob_end_flush();
