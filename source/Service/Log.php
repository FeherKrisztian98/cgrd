<?php

namespace App\Service;

class Log
{
    protected const string LOG_PATH = __DIR__ . '/../../log/errorLog.log';

    /**
     * Log an error message to the log file
     *
     * @param string $message
     *
     * @return void
     */
    public static function log(string $message): void
    {
        static $hasErrored;
        $hasErrored = $hasErrored ?? false;

        if (!$hasErrored && !Environment::isDevelopment()) {
            // Display a generic error message in production
            echo 'An error occurred';
            $hasErrored = true;
        }

        $dirName = dirname(self::LOG_PATH);
        if (!is_dir($dirName) && !mkdir($dirName, 0755, true) && !is_dir($dirName)) {
            throw new \RuntimeException('Cannot create log directory');
        }
        // Ensure the log file is writable, and create it if it doesn't exist
        if (!file_exists(self::LOG_PATH) && !touch(self::LOG_PATH)) {
            throw new \RuntimeException('Cannot create log file');
        }
        if (!is_writable(self::LOG_PATH)) {
            throw new \RuntimeException('Cannot write log file');
        }

        // Log the error message
        error_log(sprintf("%s %s%s", date('Y-m-d H:i:s'), $message, PHP_EOL), 3, self::LOG_PATH);
    }
}