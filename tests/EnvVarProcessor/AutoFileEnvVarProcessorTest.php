<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\EnvVarProcessor;

use PHPUnit\Framework\TestCase;
use Sword\SwordBundle\EnvVarProcessor\AutoFileEnvVarProcessor;

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
        putenv('FOO');
        putenv('FOO_FILE');
        if (file_exists(self::ENV_VAR_FILE)) {
            unlink(self::ENV_VAR_FILE);
        }
    }

    /**
     * @dataProvider validValues
     */
    public function testGetEnvFile($value, $expected): void
    {
        file_put_contents(self::ENV_VAR_FILE, $value);
        putenv('FOO_FILE=' . self::ENV_VAR_FILE);

        $processor = new AutoFileEnvVarProcessor();
        $result = $processor->getEnv('auto_file', 'FOO', static fn () => null);

        $this->assertSame((string) $expected, $result);
    }

    /**
     * @dataProvider validValues
     * @covers ::getEnv
     */
    public function testGetEnvValue($value, $expected): void
    {
        putenv('FOO=' . $value);

        $processor = new AutoFileEnvVarProcessor();
        $result = $processor->getEnv('auto_file', 'FOO', static fn () => null);

        $this->assertSame($expected, $result);
    }

    public function validValues(): array
    {
        return [
            ['some_value', 'some_value'],
            ['135', '135'],
            ['true', 'true'],
            ['', ''],
            [false, ''],
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
