<?php

namespace App\ENUM;

/**
 *  Represents the types of notifications that can be displayed in the application.
 *  This enum includes two types: SUCCESS and ERROR
 */
enum NotificationType
{
    case SUCCESS;

    case ERROR;

    /**
     * Get the CSS class associated with the notification type
     *
     * @return string The CSS class for the notification type.
     */
    public function getCssClass(): string
    {
        return match ($this) {
            self::ERROR => 'notification--error',
            self::SUCCESS => 'notification--success',
        };
    }
}