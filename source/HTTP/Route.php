<?php

namespace App\HTTP;

use App\Middleware\MiddlewareInterface;
use BadMethodCallException;

/**
 * Represents a route in the application, mapping a controller and an action to handle HTTP requests
 */
class Route
{
    /** @var MiddlewareInterface[] List of middlewares to be executed before the controller action */
    private array $middlewares = [];

    /**
     * Route constructor
     *
     * @param string $controllerClass The controller class to handle the route
     * @param string $action The action method on the controller
     */
    public function __construct(protected string $controllerClass, protected string $action)
    {
    }

    /**
     * Adds a middleware to the route
     *
     * @param MiddlewareInterface $middleware The middleware to add
     *
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Retrieves the list of middlewares for the route
     *
     * @return MiddlewareInterface[] An array of middlewares
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Handles the HTTP request by executing the controller action
     *
     * @param Request $request The HTTP request object
     * @param Response $response The HTTP response object
     *
     * @return Response The HTTP response after processing
     */
    public function handle(Request $request, Response $response): Response
    {
        $controller = new ($this->controllerClass)($request, $response);
        $action = $this->action;

        if (!method_exists($controller, $action)) {
            throw new BadMethodCallException("Method {$action} not found in {$this->controllerClass}");
        }

        return $controller->$action();
    }
}