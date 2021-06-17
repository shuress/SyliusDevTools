<?php

/*
 * This file is part of Monsieur Biz' devtools for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusDevtools\Command\TestApp;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class SimulateRecipeCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':simulate-recipe';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $this->getIo()->title('Simulate recipe');
        if (null === ($recipeFolder = $this->getRecipeFolder())) {
            $this->getIo()->warning('No recipe found');

            return 1;
        }

        $this->getIo()->section('Copy files from recipe');
        $progressBar = $this->getIo()->createProgressBar();
        $progressBar->start(\count($this->getRecipeFiles()));
        $this->createCopies($this->getRecipeFiles(), $recipeFolder, $progressBar);
        $progressBar->finish();
        $output->writeln('');
        $this->getIo()->success('All files has been copied');

        return 1;
    }

    /**
     * @param array $files
     * @param string $basepath
     * @param ProgressBar $progressBar
     */
    private function createCopies(array $files, string $basepath, ProgressBar $progressBar = null): void
    {
        foreach ($files as $file) {
            if ('*' === substr($file, -1)) {
                $subfiles = $this->extractFilesFromFolder(
                    $basepath . str_replace('*', '', $file),
                    $basepath
                );
                $this->createCopies($subfiles, $basepath);
                if (null !== $progressBar) {
                    $progressBar->advance();
                }
                continue;
            }

            $this->createCopy(
                $basepath . $file,
                $this->getAbsoluteProjectFolder() . $file
            );

            if (null !== $progressBar) {
                $progressBar->advance();
            }
        }
    }

    /**
     * @param string $folder
     * @param string $basepath
     *
     * @return array
     */
    private function extractFilesFromFolder(string $folder, string $basepath): array
    {
        $current = '';
        $files = [];
        foreach ((new Finder())->in($folder) as $file) {
            if ($file->isDir() && false !== $file->getRealPath() && false === strpos($file->getRealPath(), $current)) {
                $current = $file->getRealPath();
            }
            if ($file->getRealPath() === $current || (false !== ($file->getRealPath()) && false === strpos($file->getRealPath(), $current))) {
                $files[] = str_replace($basepath, '', $file->getRealPath());
            }
        }

        return $files;
    }

    /**
     * @param string $origin
     * @param string $target
     */
    private function createCopy(string $origin, string $target): void
    {
        if ($this->filesystem->exists($target) || $this->filesystem->readlink($target)) {
            $this->filesystem->remove($target);
        }
        $this->filesystem->copy($origin, $target);
    }

    /**
     * @return array
     */
    private function getRecipeFiles(): array
    {
        return [
            '/config/packages/*',
            '/config/routes/*',
        ];
    }
}
