<?php

namespace App\Middleware;

use App\HTTP\Request;
use App\HTTP\Response;
use Random\RandomException;

/**
 *  Middleware to protect against Cross-Site Request Forgery (CSRF) attacks
 *  It checks for a valid CSRF token on sensitive HTTP methods (POST, PUT, DELETE)
 */
class CsrfMiddleware implements MiddlewareInterface
{
    /**
     *  Handles CSRF token validation
     *  If the CSRF token is missing or invalid a 403 response code is returned, and the user is redirected to the home page
     *
     * @param Request $request The current HTTP request instance
     * @param Response $response The current HTTP response instance
     *
     * @throws RandomException
     * @return Response|null A redirect response if the CSRF check fails, otherwise null to continue the chain
     */
    public function handle(Request $request, Response $response): ?Response
    {
        // Initialize CSRF token if it's not already set
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateToken();
        }

        // Only check CSRF token for sensitive HTTP methods
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
            $headers = getallheaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? false;

            // If the CSRF token is missing or does not match, return a 403 response
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                return $response->redirect('/')->setHttpCode(403);
            }

            // Regenerate the CSRF token after a successful request
            $_SESSION['csrf_token'] = $this->generateToken();
        }

        // Return null to indicate that the middleware has passed successfully
        return null;
    }

    /**
     * Generates a secure CSRF token
     *
     * @throws RandomException If unable to generate random bytes.
     *
     * @return string
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}