<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Attribute;

use Symfony\Component\Console\Attribute\AsCommand;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsWordpressCommand extends AsCommand
{
}
