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

final class InstallSyliusProjectCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':install-project';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $this->getIo()->title('Install sylius');
        $this->installSyliusDatabase();
        $this->installSyliusFixtures();
        $this->installAssets();

        return 1;
    }

    private function installSyliusDatabase(): void
    {
        $this->getIo()->section('Install Sylius database');
        $this->runCommand([
            'symfony',
            'php',
            $this->getAbsoluteProjectFolder() . '/bin/console',
            'doctrine:database:drop',
            '--if-exists',
            '--force',
        ]);
        $this->runCommand([
            'symfony',
            'php',
            $this->getAbsoluteProjectFolder() . '/bin/console',
            'doctrine:database:create',
            '--if-not-exists',
        ]);
        $this->runCommand([
            'symfony',
            'php',
            $this->getAbsoluteProjectFolder() . '/bin/console',
            'doctrine:migration:migrate',
            '-n',
        ]);
        $this->getIo()->success('Database successfuly installed');
    }

    private function installSyliusFixtures(): void
    {
        $this->getIo()->section('Install Sylius fixtures');
        $this->runCommand([
            'symfony',
            'php',
            $this->getAbsoluteProjectFolder() . '/bin/console',
            'sylius:fixtures:load',
            '-n',
            'default',
        ]);
        $this->getIo()->success('Sylius fixtures successfuly played');
    }

    private function installAssets(): void
    {
        $this->getIo()->section('Install assets');
        $this->runCommand([
            'yarn',
            '--cwd',
            $this->getAbsoluteProjectFolder(),
            'install',
        ]);
        $this->runCommand([
            'yarn',
            '--cwd',
            $this->getAbsoluteProjectFolder(),
            'run',
            'gulp',
        ]);
        $this->getIo()->success('Assets successfuly generated');
    }
}
