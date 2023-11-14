#!/usr/bin/env
<?php
namespace Ksr\SchemeCli;

require __DIR__.'/ksr/intrepreter';
require __DIR__.'/vendor/autoload.php';

use Ksr\CLI\Interpreter;

$app = new Interpreter('SchemeCLI', __DIR__ . '/src/Command/commands.txt');
?>
