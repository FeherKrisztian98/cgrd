<?php

namespace Service;

use Model\User;

/**
 * Auth service class to handle user authentication and session management
 */
final class Auth
{
    /**
     * Private constructor to prevent instantiation
     */
    private function __construct()
    {

    }

    /**
     * Check if a user is logged in based on session data
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['id']);
    }

    /**
     * Log out the user by destroying the session
     *
     * @return void
     */
    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Log in a user using their username and password
     *
     * @param string $username The username of the user
     * @param string $password The password of the user (plaintext)
     *
     * @return bool
     */
    public static function login(string $username, string $password): bool
    {
        $user = User::findByName($username);

        if ($user === null) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['id'] = $user->getId();
            return true;
        }
        return false;
    }
}