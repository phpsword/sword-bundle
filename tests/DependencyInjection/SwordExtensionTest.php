<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PHPUnit\Framework\TestCase;
use Sword\SwordBundle\DependencyInjection\SwordExtension;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use Williarin\WordpressInteropBundle\DependencyInjection\WilliarinWordpressInteropExtension;

class SwordExtensionTest extends TestCase
{
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $extensions = [
            'framework' => new FrameworkExtension(),
            'security' => new SecurityExtension(),
            'doctrine' => new DoctrineExtension(),
            'wordpress_interop' => new WilliarinWordpressInteropExtension(),
            'sword' => new SwordExtension(),
        ];

        $container = new ContainerBuilder();
        $container->registerExtension($extensions['framework']);
        $container->registerExtension($extensions['security']);
        $container->registerExtension($extensions['doctrine']);
        $container->registerExtension($extensions['wordpress_interop']);
        $container->registerExtension($extensions['sword']);
        $container->setParameter('kernel.debug', true);

        $this->container = $container;
    }

    public function testSecurityIsOverriddenBySword(): void
    {
        $this->loadSwordExtensionWithConfig('app.yaml');

        $this->assertSame([
            'customer' => 'ROLE_USER',
            'subscriber' => 'customer',
            'contributor' => 'subscriber',
            'author' => 'contributor',
            'editor' => 'author',
            'shop_manager' => 'editor',
            'administrator' => 'shop_manager',
            'ROLE_SUPER_ADMIN' => [
                'administrator',
                'ROLE_ALLOWED_TO_SWITCH',
            ],
        ], $this->container->getExtensionConfig('security')[0]['role_hierarchy']);
    }

    public function testSecurityIsOverriddenByApp(): void
    {
        $this->loadSwordExtensionWithConfig('app2.yaml');

        $this->assertSame([
            'customer' => 'ROLE_ADMIN',
            'subscriber' => 'customer',
            'contributor' => 'subscriber',
            'author' => 'contributor',
            'editor' => 'author',
            'shop_manager' => 'editor',
            'administrator' => 'shop_manager',
            'ROLE_SUPER_ADMIN' => [
                'administrator',
                'ROLE_ALLOWED_TO_SWITCH',
            ],
        ], $this->container->getExtensionConfig('security')[0]['role_hierarchy']);
    }

    public function testDoctrineIsOverriddenBySword(): void
    {
        $this->loadSwordExtensionWithConfig('app.yaml');

        $this->assertSame([
            'url' => null,
            'dbname' => '%env(auto_file:WORDPRESS_DB_NAME)%',
            'host' => '%env(auto_file:WORDPRESS_DB_HOST)%',
            'user' => '%env(auto_file:WORDPRESS_DB_USER)%',
            'password' => '%env(auto_file:WORDPRESS_DB_PASSWORD)%',
            'charset' => '%env(auto_file:WORDPRESS_DB_CHARSET)%',
            'driver' => 'mysqli',
            'server_version' => '8.0',
        ], $this->container->getExtensionConfig('doctrine')[0]['dbal']);
    }

    public function testDefaultConnectionExistsWithMultipleDoctrineConnections(): void
    {
        $this->loadSwordExtensionWithConfig('app2.yaml');

        $this->assertSame([
            'default_connection' => 'default',
            'connections' => [
                'default' => [
                    'dbname' => '%env(auto_file:WORDPRESS_DB_NAME)%',
                    'host' => '%env(auto_file:WORDPRESS_DB_HOST)%',
                    'user' => '%env(auto_file:WORDPRESS_DB_USER)%',
                    'password' => '%env(auto_file:WORDPRESS_DB_PASSWORD)%',
                    'charset' => '%env(auto_file:WORDPRESS_DB_CHARSET)%',
                    'driver' => 'mysqli',
                    'server_version' => '8.0',
                    'url' => null,
                ],
                'another' => [
                    'url' => '%env(DATABASE_URL)%',
                ],
            ],
        ], $this->container->getExtensionConfig('doctrine')[0]['dbal']);
    }

    public function testDefaultConnectionCanBeOverriddenByApp(): void
    {
        $this->loadSwordExtensionWithConfig('app3.yaml');

        $this->assertSame([
            'default_connection' => 'default',
            'connections' => [
                'default' => [
                    'url' => '%env(DATABASE_URL)%',
                ],
                'another' => [
                    'url' => '%env(DATABASE_URL)%',
                ],
            ],
        ], $this->container->getExtensionConfig('doctrine')[0]['dbal']);
    }

    public function testDoctrineIsOverriddenByApp(): void
    {
        $this->loadSwordExtensionWithConfig('app4.yaml');

        $this->assertSame([
            'dbname' => 'newname',
            'host' => 'newhost',
            'user' => '%env(auto_file:WORDPRESS_DB_USER)%',
            'password' => '%env(auto_file:WORDPRESS_DB_PASSWORD)%',
            'charset' => '%env(auto_file:WORDPRESS_DB_CHARSET)%',
            'driver' => 'mysqli',
            'server_version' => '8.0',
            'url' => null,
        ], $this->container->getExtensionConfig('doctrine')[0]['dbal']);
    }

    private function loadSwordExtensionWithConfig(string $file): void
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load($file);

        $config = Yaml::parseFile(__DIR__ . "/../config/$file");

        $this->container->getExtension('sword')->prepend($this->container);
        $this->container->getExtension('sword')->load([$config['sword']], $this->container);
    }
}
