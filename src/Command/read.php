<?php
namespace Ksr\SchemeCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Read extends Command
{
    protected function configure()
    {
        $this->setName("read");
        $this->setDescription("Read scheme language declaration and return input");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Pong !");
        return Command::SUCCESS;
    }
}

?>
