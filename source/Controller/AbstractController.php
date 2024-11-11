<?php

namespace Controller;

use HTTP\Request;
use HTTP\Response;

/**
 * Serves as a base controller class that provides common functionality
 */
class AbstractController
{
    /**
     * AbstractController constructor
     *
     * @param Request $request The HTTP request instance
     * @param Response $response The HTTP response instance
     */
    public function __construct(protected Request $request, protected Response $response)
    {
    }
}