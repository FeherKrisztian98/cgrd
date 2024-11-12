<?php

namespace App\Model;

use App\Database\Database;

/** User model class representing a user in the system */
class User extends AbstractModel
{
    /** @var string The username of the user */
    public string $username;

    /** @var string The password of the user (hashed) */
    public string $password;

    /** @var string The name of the table in the database */
    protected const string DB_TABLE = 'users';

    /**
     * Find a user by their username
     *
     * @param string $username The username to search for
     *
     * @return static|null A User model instance if the user is found, or null if not
     */
    public static function findByName(string $username): ?static
    {
        $database = Database::getInstance();

        $query = $database->prepare(sprintf("SELECT id, password FROM %s WHERE username = :username", static::DB_TABLE));
        $query->bindValue(':username', $username);
        $query->execute();
        $user = $query->fetch();

        if (!$user) {
            return null;
        }

        return self::fromArray($user);
    }
}