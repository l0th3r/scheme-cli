<?php
namespace Ksr\SchemeCli;

use Ksr\SchemeCli\App\Interpreter;

require __DIR__.'/vendor/autoload.php';

$app = new Interpreter();
$app->run();