<?php

declare(strict_types=1);

namespace Sword\SwordBundle\EnvVarProcessor;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

final class AutoFileEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv): string|array|bool|null
    {
        if ($fileEnv = getenv($name . '_FILE')) {
            return rtrim(file_get_contents($fileEnv), "\r\n");
        }

        if (($val = getenv($name)) !== false) {
            return $val;
        }

        if (isset($_SERVER[$name]) && !empty($_SERVER[$name])) {
            return $_SERVER[$name];
        }

        return null;
    }

    public static function getProvidedTypes(): array
    {
        return [
            'auto_file' => 'string',
        ];
    }
}
