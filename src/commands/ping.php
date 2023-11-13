<?php
namespace Ksr\SchemeCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ping extends Command
{
    protected function configure()
    {
        $this->setName("ping");
        $this->setDescription("Respond Pong !");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Pong !");
        return Command::SUCCESS;
    }
}