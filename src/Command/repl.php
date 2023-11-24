<?php
namespace Ksr\SchemeCli\Command;

use Ksr\SchemeCli\Tools\Scheme\SchemeParser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Repl extends Command
{
    protected function configure()
    {
        $this->setName("repl");
        $this->setDescription("Read scheme language declaration, parse and evaluate, print result, start over.");

        $this->addOption("once", "o", InputOption::VALUE_NONE, "Only execture the repl once");

        $this->setHelp("To leave the REPL, type \"exit\" and press \"Enter\".");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper("question");

        $repl = -1;

        if($input->getOption("once"))
        {
            $repl = 1;
        }

        while($repl < 0 || $repl > 0)
        {
            $question = new Question("> ");
            $response = $helper->ask($input, $output, $question);

            if($response == "exit")
            {
                $repl = false;
                break;
            }
            
            if($response != "")
            {
                $parser = new SchemeParser($response, $output);
                $parser->parse();
                $parser->evaluate();
            }

            if($repl > 0)
            {
                $repl--;
            }
        }
        
        return Command::SUCCESS;
    }
}
?>
