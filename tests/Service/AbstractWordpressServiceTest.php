<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Service;

use Sword\SwordBundle\Service\AbstractWordpressService;
use PHPUnit\Framework\TestCase;

class AbstractWordpressServiceTest extends TestCase
{
    public function testGetPriority(): void
    {
        $this->assertSame(0, (new class extends AbstractWordpressService {
            public function initialize(): void
            {
            }
        })->getPriority());
    }
}
