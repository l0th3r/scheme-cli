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
        $this->setDescription("Read scheme language declarations from a file and print errors and interpretation result. Use 'verbose' (-v | -vv | -vvv) option to print more details.");

        $this->addArgument("filepath", InputArgument::REQUIRED, "Source file to read");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('filepath');

        if(file_exists($path) == false)
        {
            $output->writeln("<error>File could not be found at path: ".$path."</error>");
            return Command::FAILURE;
        }

        $file = fopen($path, "r");
        $size = filesize($path);

        if($size <= 0)
        {
            fclose($file);
            return Command::SUCCESS;
        }

        $content = fread($file, $size);
        fclose($file);

        $parser = new SchemeParser($content, $output);
        $parser->parse();
        $parser->evaluate();
        
        return Command::SUCCESS;
    }
}
?>
