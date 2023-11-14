<?php
namespace Ksr\SchemeCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Pong extends Command
{
    protected function configure()
    {
        $this->setName("pong");
        $this->setDescription("Respond Pong !");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Ping !");
        return Command::SUCCESS;
    }
}

?>
