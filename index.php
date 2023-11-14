#!/usr/bin/env
<?php
namespace Ksr\SchemeCli;

use Ksr\SchemeCli\App\Interpreter;

require __DIR__.'/vendor/autoload.php';

$app = new Interpreter('SchemeCLI', __DIR__ . '/src/Command/commands.txt');
?>
