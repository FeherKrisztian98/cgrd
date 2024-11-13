<?php

use App\Tests\Integration\NewsIntegrationTest;
use App\Tests\TestCase;
use App\Tests\Unit\NewsUnitTest;

require_once './vendor/autoload.php';

ob_start();

/** @var TestCase[] $tests */
$tests = [
    new NewsUnitTest(),
    new NewsIntegrationTest(),
];

if (empty($tests)) {
    return;
}

foreach ($tests as $test) {
    $test->runTests();
}

ob_end_flush();
