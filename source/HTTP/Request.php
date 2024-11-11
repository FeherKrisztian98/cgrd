<?php

namespace HTTP;

/** Represents a HTTP request */
class Request
{
    /** @var string Special header for checking SPA request */
    protected const string SPA_REQUEST_HEADER = 'HTTP_X_SPA_REQUEST';

    /** @var string Special header for checking SPA request origin */
    protected const string SPA_ORIGIN_HEADER = 'HTTP_X_SPA_FROM';

    /** @var array Stores the request parameters */
    protected array $params;

    /** @var bool Indicates if the request is an AJAX request */
    protected bool $isAjax;

    /** @var string The route the request is coming from */
    protected string $referer;

    /**
     * Constructor to initialize request parameters and detect AJAX requests
     */
    public function __construct()
    {
        $this->params = $_POST;

        // POST data or parsed input for PUT
        if (empty($this->params)) {
            parse_str(file_get_contents('php://input'), $this->params);
        }

        // Detect if the request is an AJAX request based on a specific header
        $this->isAjax = isset($_SERVER[self::SPA_REQUEST_HEADER]) && $_SERVER[self::SPA_REQUEST_HEADER] === 'true';
        $this->referer = $_SERVER[self::SPA_ORIGIN_HEADER] ?? '';
    }

    /**
     * Retrieves the value of a request parameter
     *
     * @param string $key The parameter name
     *
     * @return mixed
     */
    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Sets a request parameter value
     *
     * @param string $key The parameter name
     * @param mixed $value The parameter value
     *
     * @return void
     */
    public function setParam(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }

    /**
     * Check if the request is an AJAX request
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->isAjax;
    }

    /**
     * Check if the request is coming from another page
     *
     * @return bool
     */
    public function isRefererEmpty(): bool
    {
        return empty($this->referer) || $this->referer === '/';
    }


}