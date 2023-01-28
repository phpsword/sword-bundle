<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Command;

use Sword\SwordBundle\Attribute\AsWordpressCommand;
use Sword\SwordBundle\Service\WordpressService;
use WP_CLI;
use WP_CLI_Command;

abstract class WordpressCommand extends WP_CLI_Command implements WordpressService
{
    abstract public function __invoke(array $arguments, array $options);

    public function getPriority(): int
    {
        return 0;
    }

    public function initialize(): void
    {
        if (!(\defined('WP_CLI') && WP_CLI)) {
            return;
        }

        WP_CLI::add_command(static::getDefaultName(), static::class);
    }

    public static function getDefaultName(): ?string
    {
        $class = static::class;

        if ($attribute = (new \ReflectionClass($class))->getAttributes(AsWordpressCommand::class)) {
            return $attribute[0]->newInstance()->name;
        }

        return null;
    }
}
