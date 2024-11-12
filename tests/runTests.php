<?php

use App\Tests\TestCase;

require_once './vendor/autoload.php';

ob_start();

/** @var TestCase[] $tests */
$tests = [
];

if (empty($tests)) {
    return;
}

foreach ($tests as $test) {
    $test->runTests();
}

ob_end_flush();
