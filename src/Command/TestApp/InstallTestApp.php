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

final class InstallTestApp extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':install';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getSteps() as $step) {
            if (null !== ($app = $this->getApplication()) && null !== ($cmd = $app->find($step))) {
                $cmd->run($input, $output);
            }
        }

        $this->getIo()->success('You can now test your plugin on https://127.0.0.1:8000/');

        return 1;
    }

    /**
     * @return array
     */
    private function getSteps(): array
    {
        return [
            'devtools:test-app:create-project',
            'devtools:test-app:simulate-recipe',
            'devtools:test-app:add-local-package',
            'devtools:test-app:clean-project-files',
            'devtools:test-app:copy-test-app-files',
            'devtools:test-app:install-project',
        ];
    }
}
