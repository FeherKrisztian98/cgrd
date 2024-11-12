<?php

namespace App\Service;

use Throwable;

/**
 * This service class provides methods to interact with environment variables and manage the application environment
 */
class Environment
{
    /**
     * Get the value of an environment variable
     *
     * @param string $key
     *
     * @return false|string
     */
    public static function get(string $key): false|string
    {
        return getenv($key);
    }

    /**
     * Check if the application is running in a development environment
     *
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return self::get('DEVELOPMENT') === 'true';
    }

    /**
     * Bootstrap the application environment setup
     *
     * @return void
     */
    public static function bootstrap(): void
    {
        session_set_cookie_params(['samesite' => 'Strict']);
        session_start();

        // Set custom error and exception handlers
        error_reporting(static::isDevelopment() ? E_ALL : 0);
        set_error_handler([static::class, 'handleError']);
        set_exception_handler([static::class, 'handleException']);
        register_shutdown_function([static::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions
     *
     * @param Throwable $exception
     *
     * @return void
     */
    public static function handleException(Throwable $exception): void
    {
        $message = $exception->__toString();
        if (self::isDevelopment()) {
            echo $message;
        }
        Log::log($message);
    }

    /**
     * Handle fatal errors on script shutdown
     *
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if (!$error) {
            return;
        }
        Log::log($error['message']);
    }

    /**
     * Handle PHP errors such as warnings and notices
     *
     * @param int $errNumber
     * @param string $errText
     * @param string $errFile
     * @param int $errLine
     *
     * @return bool
     */
    public static function handleError(int $errNumber, string $errText, string $errFile, int $errLine): bool
    {
        Log::log("Error [$errNumber]: $errText in $errFile on line $errLine");

        // Stop default error handler from running on production env
        return !self::isDevelopment();
    }
}