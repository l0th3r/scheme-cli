<?php
namespace Ksr\SchemeCli\Command;

use Ksr\SchemeCli\Tools\Scheme\SchemeParser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Read extends Command
{
    protected function configure()
    {
        $this->setName("read");
        $this->setDescription("Read scheme language declaration and print errors and interpretation result. Use 'verbose' option to print more details.");
        $this->setHelp("Use double quotes to write a space-separated declaration.\nExemple: \"(+ 5 10) (* 5 2)\" as declaration will output:\n15\n10");

        $this->addOption("omiterr", "o", InputOption::VALUE_NONE, "Omit all errors, will only print what is sucessfully interpreted.");

        $this->addArgument("declaration", InputArgument::REQUIRED, "Scheme declaration to execute");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $declaration = $input->getArgument('declaration');

        $printErrors = true;
        $printCallStack = $output->isVerbose();
        $printDebug = $output->isDebug();

        if($input->getOption("omiterr"))
        {
            $printErrors = false;
            $printCallStack = false;
        }

        $parser = new SchemeParser($declaration, $printErrors, $printCallStack, $printDebug);
        $result = $parser->parse();

        $output->writeln($result);
        
        return Command::SUCCESS;
    }
}
?>
