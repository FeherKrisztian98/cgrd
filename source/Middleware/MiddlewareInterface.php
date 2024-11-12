<?php

namespace App\Middleware;

use App\HTTP\Request;
use App\HTTP\Response;

/**
 * This interface defines a standard for middleware classes
 */
interface MiddlewareInterface
{
    /**
     * Handle the incoming request and response.
     *
     * @param Request $request The current HTTP request instance
     * @param Response $response  The current HTTP response instance
     *
     * @return Response|null
     */
    public function handle(Request $request, Response $response): ?Response;
}