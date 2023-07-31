<?php

declare(strict_types=1);

namespace Sword\SwordBundle;

use Sword\SwordBundle\DependencyInjection\Compiler\WordpressPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SwordBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WordpressPass());
    }
}
