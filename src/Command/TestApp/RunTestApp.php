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

final class RunTestApp extends AbstractTestAppCommand
{
    protected static $defaultName = parent::CMD_NAME . ':run';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $this->getIo()->title('Run test app');
        $this->upDockerCompose();
        $this->upServe();

        return 1;
    }

    private function upDockerCompose(): void
    {
        $this->getIo()->section('Docker compose up');
        $this->runCommand([
            'docker-compose',
            '-f',
            $this->getAbsoluteProjectFolder() . '/docker-compose.yaml',
            'up',
            '-d',
        ]);
        $this->getIo()->success('Docker compose is UP');
    }

    private function upServe(): void
    {
        $this->getIo()->section('Local server up');
        $this->runCommand([
            'symfony',
            'local:server:start',
            '--dir=',
            $this->getAbsoluteProjectFolder() . '/',
            '-d',
            '--no-tls',
        ], true);
        $this->getIo()->success('Docker compose is UP');
    }
}
