<?php
namespace Ksr\SchemeCli;

use Error;
use Exception;
use Symfony\Component\Console\Application;

class Interpreter extends Application
{
    protected string $filePath = "";
    protected string $commandsNamespace = "";

    public function __construct(string $appName, string $version, string $commandFilePath)
    {
        parent::__construct();

        $this->setName($appName);
        $this->setVersion($version);
        $this->filePath = $commandFilePath;

        try
        {
            $this->importCommands();
        }
        catch (Exception $ex)
        {
            echo $this->getName() . " cli error: " . $ex->getMessage() . "\n";
            return;
        }

        try
        {
            $this->run();
        }
        catch (Exception $ex)
        {
            echo $this->getName() . " " . $ex. "\n";
            return;
        }
    }

    protected function importCommands()
    {
        if(is_dir($this->filePath) || !is_file($this->filePath))
        {
            throw new Exception("Command file path is not properly set.");
        }

        $lineIdx = 0;
        foreach($this->getFileLines($this->filePath) as $line)
        {
            $lContent = preg_replace("/\r|\n/", "", $line);
            
            if($lineIdx == 0)
            {
                $this->commandsNamespace = $lContent . "\\";
            }
            else
            {
                $commandClass = $this->commandsNamespace . ucfirst($lContent);

                try
                {
                    $this->add(new $commandClass());
                }
                catch(Error $err)
                {
                    throw new Exception("Could not find command class '" . $commandClass . "' from file " . $this->filePath);
                }
            }

            $lineIdx++;
        }
    }

    protected function assignCommand(string $commandClass)
    {
        $this->add(new $commandClass());
    }

    protected function getFileLines($path)
    {
        $file = fopen($path, "r");
        
        if($file == false)
        {
            fclose($file);
            throw new Exception("Could not open command file at " . $path);
        }

        $lines = [];

        while (($line = fgets($file)) !== false) {
            array_push($lines, $line);
        }

        fclose($file);

        return $lines;
    }
}

?>
