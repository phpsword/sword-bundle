<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Translation;

use Sword\SwordBundle\Service\AbstractWordpressService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ChildThemeTranslationInitializer extends AbstractWordpressService
{
    public function __construct(
        #[Autowire('%sword.child_theme_translation_domain%')] private readonly string $textDomain,
    ) {
    }

    public function initialize(): void
    {
        add_action('after_setup_theme', [$this, 'loadThemeLanguage']);
    }

    public function getLanguagesPath(): string
    {
        return get_stylesheet_directory() . '/languages';
    }

    public function loadThemeLanguage(): void
    {
        load_child_theme_textdomain($this->textDomain, $this->getLanguagesPath());
    }
}
