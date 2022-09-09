<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\EnvVarProcessor;

use PHPUnit\Framework\TestCase;
use Sword\SwordBundle\EnvVarProcessor\AutoFileEnvVarProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoFileEnvVarProcessorTest extends TestCase
{
    private const ENV_VAR_FILE = __DIR__ . '/../../var/test/env_var.test';

    protected function setUp(): void
    {
        if (!file_exists(dirname(self::ENV_VAR_FILE))) {
            mkdir(dirname(self::ENV_VAR_FILE), 0755, true);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists(self::ENV_VAR_FILE)) {
            unlink(self::ENV_VAR_FILE);
        }
    }

    /**
     * @dataProvider validValues
     * @covers ::getEnv
     */
    public function testGetEnvFile($value, $expected): void
    {
        file_put_contents(self::ENV_VAR_FILE, $value);

        $container = new ContainerBuilder();
        $container->setParameter('env(FOO_FILE)', self::ENV_VAR_FILE);
        $container->compile();

        $processor = new AutoFileEnvVarProcessor($container);

        $result = $processor->getEnv('auto_file', 'FOO', function ($name) {
            if ($name === 'FOO_FILE') {
                return self::ENV_VAR_FILE;
            }

            $this->fail('This should not happen.');
        });

        $this->assertSame((string) $expected, $result);
    }

    /**
     * @dataProvider validValues
     * @covers ::getEnv
     */
    public function testGetEnvValue($value, $expected): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('env(FOO)', $value);
        $container->compile();

        $processor = new AutoFileEnvVarProcessor($container);

        $result = $processor->getEnv('auto_file', 'FOO', function ($name) use ($value) {
            if ($name === 'FOO_FILE') {
                return null;
            }

            if ($name === 'FOO') {
                return $value;
            }

            $this->fail('This should not happen.');
        });

        $this->assertSame($expected, $result);
    }

    public function validValues(): array
    {
        return [
            ['some_value', 'some_value'],
            ['135', '135'],
            ['true', 'true'],
            [null, null],
            [false, null],
        ];
    }

    /**
     * @covers ::getProvidedTypes
     */
    public function testGetProvidedTypes(): void
    {
        $this->assertSame(['auto_file' => 'string'], AutoFileEnvVarProcessor::getProvidedTypes());
    }
}
