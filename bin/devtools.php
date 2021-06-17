<?php

set_time_limit(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$autoloaders = [];

if (file_exists(__DIR__ . '/../autoload.php')) {
    include_once __DIR__ . '/../autoload.php';
} else {
    $autoloaders = [
        __DIR__ . '/../../../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
        __DIR__ . '/../vendor/autoload.php'
    ];
}


foreach ($autoloaders as $file) {
    if (file_exists($file)) {
        $autoloader = $file;
        break;
    }
}

if (isset($autoloader)) {
    $autoload = include_once $autoloader;
} else {
    echo ' You must set up the project dependencies using `composer install`' . PHP_EOL;
    exit(1);
}

use MonsieurBiz\SyliusDevtools\Command\TestApp\CreateSyliusProjectCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\CleanProjectFilesCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\InstallTestApp;
use MonsieurBiz\SyliusDevtools\Command\TestApp\CopyTestAppFilesCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\SimulateRecipeCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\AddLocalPackageCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\InstallSyliusProjectCommand;
use MonsieurBiz\SyliusDevtools\Command\TestApp\RunTestApp;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

$filesystem = new Filesystem;
$config = [
    'sources_path' => __DIR__ . '/../src/Resources/sources',
    'templates_path' => __DIR__ . '/../src/Resources/templates',
];

$application = new Application('sylius-devtools', '1.0.0');
$application->add(new InstallTestApp($filesystem, $config));
$application->add(new CreateSyliusProjectCommand($filesystem, $config));
$application->add(new CleanProjectFilesCommand($filesystem, $config));
$application->add(new CopyTestAppFilesCommand($filesystem, $config));
$application->add(new SimulateRecipeCommand($filesystem, $config));
$application->add(new AddLocalPackageCommand($filesystem, $config));
$application->add(new InstallSyliusProjectCommand($filesystem, $config));
$application->add(new RunTestApp($filesystem, $config));
$application->run();



