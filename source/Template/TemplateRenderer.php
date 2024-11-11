<?php

namespace Template;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TemplateRenderer
{
    /** @var TemplateRenderer TemplateRenderer The single instance of the TemplateRenderer class */
    protected static self $instance;

    /** @var Environment Environment The Twig environment for rendering templates */
    protected Environment $engine;

    /** Singleton class responsible for rendering templates using the Twig template engine */
    private function __construct()
    {
        // Setup Twig
        $loader = new FilesystemLoader(__DIR__ . '/Views');
        $this->engine = new Environment($loader, [
            'cache' => false,
        ]);
    }

    /**
     * Get the single instance of the TemplateRenderer class
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Renders a template with the provided data
     */
    public function render(string $template, array $data): string
    {
        return $this->engine->render($template . '.twig', $data);
    }
}