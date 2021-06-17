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

final class CopyTestAppFilesCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':copy-test-app-files';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        if (null === ($sourcePath = $this->getConfig('sources_path')) || null === ($projectFolder = $this->getAbsoluteProjectFolder())) {
            return 1;
        }
        $this->getIo()->title('Copy test app files');
        $this->copyFiles(
            $this->getFileToReplace(),
            $sourcePath,
            $projectFolder
        );
        $output->writeln('');
        $this->getIo()->success('All files have been copied');

        return 1;
    }

    /**
     * @return array
     */
    private function getFileToReplace(): array
    {
        return [
            '/doctrine.yaml' => '/config/packages/doctrine.yaml',
            '/docker-compose.yaml' => '/docker-compose.yaml',
        ];
    }
}
