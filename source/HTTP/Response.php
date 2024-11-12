<?php

namespace App\HTTP;

use App\ENUM\NotificationType;
use App\Template\TemplateRenderer;
use JsonException;

/**
 * Represents a HTTP response
 */
class Response
{
    /** @var string|array The result of the response */
    protected string|array $result;

    /** @var ?Notification The optional notification to be sent with the response */
    protected ?Notification $notification = null;

    /** @var int HTTP code of the response (defaults to 200) */
    protected int $httpCode = 200;

    /**
     * Renders a page template and sets the result
     *
     * @param string $templatePath The path to the template file
     * @param array $data The data to pass to the template
     *
     * @return static Returns the current Response instance for method chaining
     */
    public function page(string $templatePath, array $data = []): static
    {
        $this->result = TemplateRenderer::getInstance()->render($templatePath, $data);

        return $this;
    }

    /**
     * Renders a view template and sets the result to include the rendered content and a CSRF token
     *
     * @param string $templatePath The path to the template file to be rendered
     * @param array $data The data to be passed to the template for rendering
     *
     * @return static Returns the current Response instance for method chaining
     */
    public function view(string $templatePath, array $data = []): static
    {
        $this->result = [
            'content' => TemplateRenderer::getInstance()->render($templatePath, $data),
            'csrfToken' => $_SESSION['csrf_token'],
        ];

        return $this;
    }

    /**
     * Sets the result to redirect to a specific URL
     *
     * @param string $url The URL to redirect to
     * @param bool $replaceHistory Whether to replace the browser history entry for the redirect
     *
     * @return $this
     */
    public function redirect(string $url, bool $replaceHistory = false): static
    {
        $this->result = [
            'redirect' => $url,
            'replaceHistory' => $replaceHistory,
        ];

        return $this;
    }

    /**
     * Converts the response to a string representation
     * @throws JsonException
     *
     * @return string
     */
    public function __toString(): string
    {
        if (is_array($this->result)) {
            if (isset($this->notification)) {
                $this->result['notification'] = $this->notification;
            }
            return json_encode($this->result, JSON_THROW_ON_ERROR);
        }
        return $this->result;
    }

    /**
     * Sets the notification to be sent with the response
     * @param $message
     * @param NotificationType $notificationType
     *
     * @return $this
     */
    public function setNotification($message, NotificationType $notificationType = NotificationType::SUCCESS): static
    {
        $this->notification = new Notification($message, $notificationType);

        return $this;
    }

    /**
     * Set the HTTP code for the response
     *
     * @param int $code HTTP code
     *
     * @return $this
     */
    public function setHttpCode(int $code): static
    {
        $this->httpCode = $code;

        return $this;
    }

    /**
     * Get the HTTP code for the response
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}