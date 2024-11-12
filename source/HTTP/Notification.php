<?php

namespace App\HTTP;

use App\ENUM\NotificationType;
use JsonSerializable;

/**
 * Represents a notification message
 */
class Notification implements JSONSerializable
{
    /**
     * Notification constructor
     *
     * @param string $message The message content of the notification
     * @param NotificationType $type The type of notification (defaults to SUCCESS)
     */
    public function __construct(protected string $message, protected NotificationType $type = NotificationType::SUCCESS)
    {
    }

    /**
     * Serializes the notification object to an array for JSON encoding.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type->getCssClass(),
            'message' => $this->message,
        ];
    }
}