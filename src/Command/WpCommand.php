<?php

namespace Sword\SwordBundle\Command;

use Sword\SwordBundle\Loader\WordpressGlobals;
use Sword\SwordBundle\Loader\WordpressLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use WP_CLI\Bootstrap\BootstrapStep;
use WP_CLI\Bootstrap\DeclareMainClass;
use WP_CLI\Bootstrap\DeclareAbstractBaseCommand;
use WP_CLI\Bootstrap\IncludeFrameworkAutoloader;
use WP_CLI\Bootstrap\ConfigureRunner;
use WP_CLI\Bootstrap\InitializeColorization;
use WP_CLI\Bootstrap\InitializeLogger;
use WP_CLI\Bootstrap\DefineProtectedCommands;
use WP_CLI\Bootstrap\LoadExecCommand;
use WP_CLI\Bootstrap\LoadRequiredCommand;
use WP_CLI\Bootstrap\IncludePackageAutoloader;
use WP_CLI\Bootstrap\IncludeFallbackAutoloader;
use WP_CLI\Bootstrap\RegisterFrameworkCommands;
use WP_CLI\Bootstrap\RegisterDeferredCommands;
use WP_CLI\Bootstrap\InitializeContexts;
use WP_CLI\Bootstrap\LaunchRunner;

#[AsCommand(
    name: 'wp',
    description: 'Run wp-cli commands',
)]
class WpCommand extends Command
{
    public function __construct(
        private readonly WordpressLoader $wordpressLoader,
        #[Autowire('%sword.wordpress_core_dir%')] private readonly string $wordpressDirectory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        global $wordpressLoader, $argv, $argc, $wpCliBootstrapState;

        $wordpressLoader = $this->wordpressLoader;

        foreach (WordpressGlobals::GLOBALS as $global) {
            global $$global;
        }

        $argv = array_slice($argv, 1, $argc - 1);
        $argv[] = sprintf('--path=%s', $this->wordpressDirectory);
        $argv = array_unique($argv);
        $argc = count($argv);

        foreach ($this->getBootstrapStates() as $step) {
            /** @var BootstrapStep $stepInstance */
            $stepInstance = new $step();
            $wpCliBootstrapState = $stepInstance->process($wpCliBootstrapState);
        }

        return Command::SUCCESS;
    }

    private function getBootstrapStates(): array
    {
        return [
            DeclareMainClass::class,
            DeclareAbstractBaseCommand::class,
            IncludeFrameworkAutoloader::class,
            ConfigureRunner::class,
            InitializeColorization::class,
            InitializeLogger::class,
            DefineProtectedCommands::class,
            LoadExecCommand::class,
            LoadRequiredCommand::class,
            IncludePackageAutoloader::class,
            IncludeFallbackAutoloader::class,
            RegisterFrameworkCommands::class,
            RegisterDeferredCommands::class,
            InitializeContexts::class,
            LaunchRunner::class,
        ];
    }
}
