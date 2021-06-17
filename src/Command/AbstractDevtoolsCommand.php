<?php

/*
 * This file is part of Monsieur Biz' devtools for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusDevtools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class AbstractDevtoolsCommand extends Command
{
    protected const CMD_NAME = 'devtools';

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var array
     */
    protected array $config;

    /**
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * @var SymfonyStyle
     */
    protected SymfonyStyle $ioStyle;

    /**
     * @param Filesystem $filesystem
     * @param array $config
     */
    public function __construct(Filesystem $filesystem, array $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
        parent::__construct(null);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    protected function getConfig(string $key): ?string
    {
        return $this->config[$key] ?? null;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ioStyle = new SymfonyStyle($input, $output);
        $this->input = $input;
        $this->output = $output;

        return 1;
    }

    /**
     * @return SymfonyStyle
     */
    protected function getIo(): SymfonyStyle
    {
        return $this->ioStyle;
    }

    /**
     * @param array $files
     * @param string $sourceBasepath
     * @param string $destBasepath
     */
    protected function copyFiles(array $files, string $sourceBasepath, string $destBasepath): void
    {
        $progressBar = $this->getIo()->createProgressBar();
        $progressBar->start(\count($files));

        foreach ($files as $source => $target) {
            $source = $sourceBasepath . $source;
            $target = $destBasepath . $target;
            if ($this->filesystem->exists($target) || $this->filesystem->readlink($target)) {
                $this->filesystem->remove($target);
            }
            $this->filesystem->copy($source, $target);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    /**
     * @param array $command
     * @param bool $forceVerbosity
     */
    protected function runCommand(array $command, bool $forceVerbosity = false): void
    {
        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run(function(string $type, string $buffer) use ($forceVerbosity): void {
            if ($this->output->isVerbose() || $forceVerbosity) {
                $this->output->write($buffer);
            }
        });
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
