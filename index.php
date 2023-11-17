<?php
namespace Ksr\SchemeCli;

require __DIR__.'/vendor/autoload.php';

use Ksr\CLI\Interpreter;

$app = new Interpreter('SchemeCLI', "0.0.1", __DIR__ . '/src/Command/commands.txt');
?>
