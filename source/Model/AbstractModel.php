<?php

namespace Model;

use Database\Database;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Abstract model class that provides basic functionality for interacting with a database
 * All model classes should extend this class
 */
abstract class AbstractModel
{
    /** @var string The table name associated with the model */
    protected const string TABLE_NAME = '';

    /** @var Database The database connection instance */
    protected Database $databaseConnection;

    /**
     * AbstractModel constructor
     *
     * Initializes the model with an optional ID
     * If the ID is provided and greater than 0, the model is loaded from the database
     *
     * @param int $id The ID of the model
     */
    public function __construct(protected int $id = 0)
    {
        $this->databaseConnection = Database::getInstance();

        if ($id > 0) {
            $this->loadById($id);
        }
    }

    /**
     * Load model data from the database by its ID
     *
     * @param int $id The ID of the model to load
     *
     * @throws PDOException If the query fails
     */
    protected function loadById(int $id): void
    {
        $stmt = $this->databaseConnection->prepare(sprintf("SELECT * FROM %s WHERE id = ?", static::TABLE_NAME));
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return;
        }
        foreach ($row as $column => $value) {
            if (property_exists($this, $column)) {
                $this->$column = $value;
            }
        }
    }

    /**
     * Delete the model from the database by its ID
     *
     * @param int $id The ID of the model to delete
     *
     * @return void
     */
    public static function deleteById(int $id): void
    {
        $stmt = Database::getInstance()->prepare(sprintf("DELETE FROM %s WHERE id = ?", static::TABLE_NAME));
        $stmt->execute([$id]);
    }

    /**
     * Retrieve all records as an array of models
     *
     * @return static[] An array of model instances, indexed by their ID
     */
    public static function findAll(): array
    {
        $stmt = Database::getInstance()->query(sprintf("SELECT * FROM %s", static::TABLE_NAME));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $model = new static();
            foreach ($row as $column => $value) {
                if (property_exists($model, $column)) {
                    $model->$column = $value;
                }
            }
            $models[$row['id']] = $model;
        }

        return $models;
    }

    /**
     * Create a model instance from an associative array of data
     *
     * @param array $data An associative array where the keys are property names and the values are property values
     *
     * @return static A new model instance populated with the provided data
     */
    public static function fromArray(array $data): static
    {
        $model = new static();
        foreach ($data as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = $value;
            }
        }
        return $model;
    }

    /**
     * Get the ID of the model
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Save the model to the database
     * If the model ID is 0, a new record is created. Otherwise, an existing record is updated
     *
     * @return void
     */
    public function save(): void
    {
        if ($this->id === 0) {
            $this->dbCreate();
        } else {
            $this->dbModify();
        }
    }

    /**
     * Insert a new record into the database based on class properties
     *
     * @return void
     */
    protected function dbCreate(): void
    {
        $columns = $this->getColumns();
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $columnNames = implode(', ', array_keys($columns));

        $stmt = $this->databaseConnection->prepare(sprintf("INSERT INTO %s ($columnNames) VALUES ($placeholders)", static::TABLE_NAME));

        $stmt->execute(array_values($columns));
        $this->id = (int)$this->databaseConnection->lastInsertId();
    }

    /**
     * Get the column names and their corresponding values
     *
     * @return array An associative array where the keys are column names and the values are the corresponding property values of the model
     */
    protected function getColumns(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $columns = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $columns[$name] = $this->$name;
        }

        return $columns;
    }

    /**
     * Update an existing record in the database
     *
     * @return void
     */
    protected function dbModify(): void
    {
        $columns = $this->getColumns();
        $setClause = implode(' = ?, ', array_keys($columns)) . ' = ?';

        $stmt = $this->databaseConnection->prepare(sprintf("UPDATE %s SET $setClause WHERE id = ?", static::TABLE_NAME));

        $stmt->execute([...array_values($columns), $this->id]);
    }
}
