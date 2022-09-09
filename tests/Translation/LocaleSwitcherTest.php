<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Translation;

use Sword\SwordBundle\Test\WordpressTestCase;
use Sword\SwordBundle\Translation\LocaleSwitcher;
use Symfony\Component\Translation\LocaleSwitcher as SymfonyLocaleSwitcher;

use function Brain\Monkey\Functions\expect;

class LocaleSwitcherTest extends WordpressTestCase
{
    private readonly SymfonyLocaleSwitcher $symfonyLocaleSwitcher;
    private readonly LocaleSwitcher $localeSwitcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->symfonyLocaleSwitcher = new SymfonyLocaleSwitcher('en', []);
        $this->localeSwitcher = new LocaleSwitcher($this->symfonyLocaleSwitcher);
    }

    public function testInitialize(): void
    {
        $this->assertEquals('en', $this->symfonyLocaleSwitcher->getLocale());

        expect('get_locale')->once()->withNoArgs()->andReturn('fr_FR');
        $this->localeSwitcher->initialize();

        $this->assertEquals('fr_FR', $this->symfonyLocaleSwitcher->getLocale());
    }

    public function testGetPriority(): void
    {
        $this->assertSame(1000, $this->localeSwitcher->getPriority());
    }
}
