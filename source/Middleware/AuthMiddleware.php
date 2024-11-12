<?php

namespace App\Middleware;

use App\HTTP\Request;
use App\HTTP\Response;
use App\Service\Auth;

/**
 * Middleware to check if a user is authenticated. If the user is not logged in, they will be redirected to a specified login page
 *
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * AuthMiddleware constructor
     *
     * @param string $redirectPath The path to redirect if the user is not authenticated
     *
     */
    public function __construct(protected string $redirectPath = '/login')
    {
    }

    /**
     * Handles the authentication check
     *
     * @param Request $request The current HTTP request instance
     * @param Response $response The current HTTP response instance
     *
     * @return Response|null
     */
    public function handle(Request $request, Response $response): ?Response
    {
        if (!Auth::isLoggedIn()) {
            // Redirect to the specified login path and stop further processing
            return $response->redirect($this->redirectPath, true);
        }
        // Return null to indicate that the middleware has passed successfully
        return null;
    }
}