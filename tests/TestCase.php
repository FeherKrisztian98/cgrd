<?php

namespace App\Tests;

/**
 * Provides common functionality to all tests
 */
abstract class TestCase
{
    /**
     * This method finds and runs all test methods prefixed with 'test'
     */
    public function runTests(): void
    {
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (str_starts_with($method->name, 'test')) {
                try {
                    $this->setup();
                    $this->{$method->name}();
                    $this->cleanup();
                    echo "{$method->name} PASS\n";
                } catch (\Exception $e) {
                    echo "{$method->name} FAIL: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    /**
     * Runs before every test method
     *
     * @return void
     */
    protected function setup(): void
    {
    }

    /**
     * Runs after every test method
     *
     * @return void
     */
    protected function cleanup(): void
    {
    }
}