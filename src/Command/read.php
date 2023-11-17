<?php
namespace Ksr\SchemeCli\Command;

use Ksr\SchemeCli\Tools\Scheme\SchemeParser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Read extends Command
{
    protected function configure()
    {
        $this->setName("read");
        $this->setDescription("Read scheme language declaration and return result");
        $this->setHelp("Use double quotes to write a space-separated declaration.\nExemple: \"(+ 5 10) (* 5 2)\" as declaration will output:\n15\n10");

        $this->addArgument("declaration", InputArgument::REQUIRED, 'Scheme declaration to execute');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $declaration = $input->getArgument('declaration');

        SchemeParser::parse($declaration);

        // if(strlen($response->result) > 0)
        //     $output->writeln($response->result."\n");

        // if($response->hasError)
        // {
        //     $output->writeln("<fg=red>".$response->error."</>");
        //     return Command::FAILURE;
        // }

        return Command::SUCCESS;
    }
}
?>
