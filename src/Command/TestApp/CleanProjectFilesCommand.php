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
use Symfony\Component\Finder\Finder;

final class CleanProjectFilesCommand extends AbstractTestAppCommand
{
    /**
     * @var string
     */
    protected static $defaultName = parent::CMD_NAME . ':clean-project-files';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $this->getIo()->title('Remove unused files');
        $finder = new Finder();

        $progressBar = $this->getIo()->createProgressBar();
        $progressBar->start(\count($this->getFilesToDelete()));
        foreach ($this->getFilesToDelete() as $target) {
            $target = $this->getAbsoluteProjectFolder() . $target;
            if ('*' === substr($target, -1)) {
                $target = $finder->in(str_replace('*', '', $target));
            }
            $this->filesystem->remove($target);
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('');
        $this->getIo()->success('All unused files have been deleted');

        return 1;
    }

    /**
     * @return array
     */
    private function getFilesToDelete(): array
    {
        return [
            '/.github',
            '/assets',
            '/docker',
            '/etc',
            '/features',
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
