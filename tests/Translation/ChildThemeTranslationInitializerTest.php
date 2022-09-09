<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Translation;

use Sword\SwordBundle\Test\WordpressTestCase;
use Sword\SwordBundle\Translation\ChildThemeTranslationInitializer;

use function Brain\Monkey\Functions\expect;

class ChildThemeTranslationInitializerTest extends WordpressTestCase
{
    private readonly ChildThemeTranslationInitializer $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChildThemeTranslationInitializer('foo');
    }

    public function testInitialize(): void
    {
        $this->service->initialize();
        $this->assertSame(10, has_action(
            'after_setup_theme',
            ChildThemeTranslationInitializer::class . '->loadThemeLanguage()',
        ));
    }

    public function testGetLanguagesPath(): void
    {
        expect('get_stylesheet_directory')->once()->andReturn('wp/content/theme/mychildtheme');
        $this->assertSame('wp/content/theme/mychildtheme/languages', $this->service->getLanguagesPath());
    }

    public function testLoadThemeLanguage(): void
    {
        expect('get_stylesheet_directory')->once()->andReturn('wp/content/theme/mychildtheme');
        expect('load_child_theme_textdomain')
            ->once()
            ->with('foo', 'wp/content/theme/mychildtheme/languages')
            ->andReturn();

        $this->service->loadThemeLanguage();

        $this->assertTrue(true);
    }
}
