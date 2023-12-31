<?php
namespace Ksr\SchemeCli\Command;

use Ksr\SchemeCli\Tools\Scheme\SchemeParser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Interpret extends Command
{
    protected function configure()
    {
        $this->setName("interpret");
        $this->setDescription("Read scheme language declaration and print errors and interpretation result. Use 'verbose' (-v | -vv | -vvv) option to print more details.");
        $this->setHelp("Use double quotes to write a space-separated declaration.\nExemple: \"(+ 5 10) (* 5 2)\" as declaration will output:\n15\n10");

        $this->addArgument("declaration", InputArgument::REQUIRED, "Scheme declaration to execute");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $declaration = $input->getArgument('declaration');

        $parser = new SchemeParser($declaration, $output);
        $parser->parse();
        $parser->evaluate();
        
        return Command::SUCCESS;
    }
}
?>
