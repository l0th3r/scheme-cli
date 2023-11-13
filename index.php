<?php
namespace Ksr\SchemeCli;

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

use Ksr\SchemeCli\Command\Ping;

$application = new Application();

$application->add(new Ping());

$application->run();