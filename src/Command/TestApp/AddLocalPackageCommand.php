<?php

/*
 * This file is part of Monsieur Biz' devtools for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusDevtools\Command\TestApp;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class AddLocalPackageCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':add-local-package';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        if ($input->hasOption('package') && null !== ($package = $input->getOption('package')) && \is_string($package)) {
            $this->getIo()->title('Add local package');
            $this->addRepoToComposer();
            $this->addLocalDependency($package);
        }

        return 1;
    }

    private function addLocalDependency(string $package): void
    {
        $this->getIo()->section('Require local dependency');
        $process = new Process([
            'symfony',
            'composer',
            'require',
            '--prefer-source',
            '-d',
            $this->getAbsoluteProjectFolder(),
            $package . ':dev-master',
        ]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function addRepoToComposer(): void
    {
        $this->getIo()->section('Add local repo to composer.json');
        $process = new Process([
            'symfony',
            'composer',
            'config',
            '-f',
            $this->getAbsoluteProjectFolder() . '/composer.json',
            'repositories.local',
            '{"type": "path", "url": "../..", "options": {"symlink": true}}',
        ]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * @return array
     */
    private function getFilesToDelete(): array
    {
        return [
            '/.github',
            '/assets',
            '/config/packages/*',
            '/config/routes/*',
            '/config/jwt',
            '/config/routes.yaml',
            '/config/secret',
            '/config/services.yaml',
            '/config/services_test.yaml',
            '/config/services_test_cached.yaml',
            '/docker',
            '/etc',
            '/features',
            '/public/*',
            '/src/Entity/*',
            '/src/Migrations',
            '/templates/bundles',
            '/.dockerignore',
            '/.editorconfig',
            '/.env.prod',
            '/.env.test_cached',
            '/behat.yml.dist',
            '/docker-compose.prod.yml',
            '/docker-compose.yml',
            '/Dockerfile',
            '/easy-coding-standard.yml',
            '/phpspec.yaml.dist',
            '/phpstan.neon',
            '/phpunit.xml.dist',
            '/README.md',
            '/yarn.lock',
        ];
    }
}
