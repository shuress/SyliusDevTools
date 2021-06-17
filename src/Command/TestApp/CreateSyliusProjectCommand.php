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

final class CreateSyliusProjectCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':create-project';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $this->getIo()->title(sprintf('Create Sylius v%s project', $this->getSyliusVersion()));

        if (null !== ($path = $this->getAbsoluteProjectFolder()) && $this->filesystem->exists($path)) {
            $this->getIo()->section('Delete existing target folder');
            $this->filesystem->remove($path);
            $this->getIo()->success('Existing folder successfully deleted');
        }

        $this->getIo()->section('Launch project-install');
        $this->getIo()->warning('This operation could takes time');
        $this->runCommand([
            'symfony',
            'composer',
            'create-project',
            'sylius/sylius-standard=' . $this->getSyliusVersion(),
            '-d',
            $this->getAbsoluteTargetFolder(),
            $this->getProjectName(),
        ]);

        $this->getIo()->success(
            sprintf('Successfuly installed Sylius v%s in %s', $this->getSyliusVersion(), $this->getProjectFolder())
        );

        return 1;
    }
}
