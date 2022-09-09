<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test;

use PHPUnit\Framework\TestCase;

use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

abstract class WordpressTestCase extends TestCase
{
    protected function setUp(): void
    {
        setUp();
    }

    protected function tearDown(): void
    {
        tearDown();
    }
}
