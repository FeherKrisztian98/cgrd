<?php

namespace App\HTTP;

use App\Controller\AppController;
use App\Controller\AuthController;
use App\Controller\NewsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\MiddlewareInterface;

/**
 * Manages the registration and dispatching of routes, including middleware processing
 */
final class Router
{
    /** @var MiddlewareInterface[] Global middlewares for every route */
    protected array $globalMiddlewares = [];

    /** @var Route[][] List of registered routes grouped by HTTP method */
    private array $routes = [];

    /**
     * Router constructor is protected to enforce singleton-like usage
     */
    protected function __construct()
    {

    }

    /**
     * Registers all the application routes and returns a Router instance
     *
     * @return Router
     */
    public static function register(): Router
    {
        $router = new self();

        $router->addGlobalMiddleware(new CsrfMiddleware());

        $authMiddleware = new AuthMiddleware();

        // Guest routes
        $router->addRoute('GET', '', [AppController::class, 'index']);
        $router->addRoute('GET', '/404', [AppController::class, 'notFound']);
        $router->addRoute('GET', '/login', [AuthController::class, 'login']);
        $router->addRoute('GET', '/logout', [AuthController::class, 'logout']);
        $router->addRoute('POST', '/auth', [AuthController::class, 'auth']);

        // Admin routes
        $router->addRoute('GET', '/news', [NewsController::class, 'listNews'])->addMiddleware($authMiddleware);
        $router->addRoute('POST', '/news/create', [NewsController::class, 'createNews'])->addMiddleware($authMiddleware);
        $router->addRoute('GET', '/news/modify/{id}', [NewsController::class, 'modifyNews'])->addMiddleware($authMiddleware);
        $router->addRoute('PUT', '/news/modify/{id}', [NewsController::class, 'updateNews'])->addMiddleware($authMiddleware);
        $router->addRoute('DELETE', '/news/delete/{id}', [NewsController::class, 'deleteNews'])->addMiddleware($authMiddleware);

        return $router;
    }

    /**
     * Adds a global middleware that applies to all routes
     *
     * @param MiddlewareInterface $middleware
     *
     * @return void
     */
    private function addGlobalMiddleware(MiddlewareInterface $middleware): void
    {
        $this->globalMiddlewares[] = $middleware;
    }

    /**
     * Registers a new route
     *
     * @param string $method The HTTP method
     * @param string $uri The route URI pattern
     * @param array $action The controller class and method
     *
     * @return Route The registered route instance
     */
    public function addRoute(string $method, string $uri, array $action): Route
    {
        return $this->routes[$method][$uri] = new Route($action[0], $action[1]);
    }

    /**
     * Dispatches the request to the appropriate route
     *
     * @param string $method The HTTP method of the request
     * @param string $uri The requested URI
     * @return Response The response object after processing the route
     *
     * @throws \Exception If the route or method is not found
     */
    public function dispatch(string $method, string $uri): Response
    {
        $uri = rtrim($uri, '/');

        $request = new Request();
        $response = new Response();

        // Handle loading pretty URLs
        if ($uri !== '' && !$request->isAjax()) {
            $uri = '';
        }

        // Check if the method exists in the routes
        if (!isset($this->routes[$method])) {
            throw new \RuntimeException("HTTP method not supported: {$method}");
        }

        // Match the URI to registered routes
        foreach ($this->routes[$method] as $routeUri => $route) {
            $pattern = '@^' . preg_replace('/\{(\w+)}/', '(?P<$1>[^/]+)', $routeUri) . '$@';

            $matches = [];
            if ($uri === $routeUri || preg_match($pattern, $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $request->setParam($key, $value);
                    }
                }

                // Merge global and route-specific middlewares
                $middlewares = array_merge($this->globalMiddlewares, $route->getMiddlewares());

                // Handle middlewares in a unified pipeline
                return $this->handleMiddlewares($middlewares, $request, $response, $route);
            }
        }

        // Route not found, redirect to 404
        return $response->redirect('/404', true)->setHttpCode(404);
    }

    /**
     * Handles the middleware pipeline and invokes the final route action
     *
     * @param MiddlewareInterface[] $middlewares List of middlewares to process
     * @param Request $request The HTTP request object
     * @param Response $response The HTTP response object
     * @param Route $route The matched route
     *
     * @return Response The final response after middleware and route handling
     */
    private function handleMiddlewares(array $middlewares, Request $request, Response $response, Route $route): Response
    {
        foreach ($middlewares as $middleware) {
            $middlewareResponse = $middleware->handle($request, $response);
            if ($middlewareResponse instanceof Response) {
                return $middlewareResponse;
            }
        }

        return $route->handle($request, $response);
    }
}