<?php

/*
 * This file is part of Monsieur Biz' devtools for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusDevtools\Command\TestApp;

use MonsieurBiz\SyliusDevtools\Command\AbstractDevtoolsCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractTestAppCommand extends AbstractDevtoolsCommand
{
    protected const CMD_NAME = parent::CMD_NAME . ':test-app';

    protected function configure(): void
    {
        $this
            ->addOption('sylius-version', 's', InputOption::VALUE_OPTIONAL, 'Sylius version to install', '1.9.1')
            ->addOption('folder', 'f', InputOption::VALUE_OPTIONAL, 'Installation destination folder', 'tests')
            ->addOption('project-name', 'p', InputOption::VALUE_OPTIONAL, 'Installation destination folder', 'Application')
            ->addOption('recipe', 'r', InputOption::VALUE_OPTIONAL, 'Recipie version', '1.0-dev')
            ->addOption('package', 'p', InputOption::VALUE_OPTIONAL, 'Local package to tests')
        ;
    }

    /**
     * @return string|null
     */
    protected function getSyliusVersion(): ?string
    {
        $version = $this->input->getOption('sylius-version');
        if (\is_string($version)) {
            return $version;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getProjectName(): ?string
    {
        $name = $this->input->getOption('project-name');
        if (\is_string($name)) {
            return $name;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getTargetFolder(): ?string
    {
        $folder = $this->input->getOption('folder');
        if (\is_string($folder)) {
            return $folder;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getAbsoluteTargetFolder(): ?string
    {
        if (false !== ($basepath = getcwd()) && null !== ($folder = $this->getTargetFolder())) {
            return $basepath . '/' . $folder;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getProjectFolder(): ?string
    {
        if (null !== ($target = $this->getTargetFolder()) && null !== ($name = $this->getProjectName())) {
            return $target . '/' . $name;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getAbsoluteProjectFolder(): ?string
    {
        if (null !== ($target = $this->getAbsoluteTargetFolder()) && null !== ($name = $this->getProjectName())) {
            return $target . '/' . $name;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getRecipeFolder(): ?string
    {
        if ($this->input->hasOption('recipe')
            && false !== ($basepath = getcwd())
            && \is_string($this->input->getOption('recipe'))
        ) {
            return $basepath . '/recipes/' . $this->input->getOption('recipe');
        }

        return null;
    }
}
