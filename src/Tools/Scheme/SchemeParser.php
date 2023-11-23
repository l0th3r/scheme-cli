<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Operation\SchemeOperation;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeExpression;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;

use Symfony\Component\Console\Output\OutputInterface;

// TODO FIX THE NEED TO REQUIRE TO SEARCH AMONG CLASSES
require_once __DIR__.'/Operation/SchemeAdd.php';
require_once __DIR__.'/Operation/SchemeMultiply.php';
require_once __DIR__.'/Operation/SchemeDivide.php';
require_once __DIR__.'/Operation/SchemeSubstract.php';
require_once __DIR__.'/Operation/SchemeModulo.php';

/**
 * Scheme language parser, provide a scheme interpretation context
 *
 * @link https://en.wikipedia.org/wiki/Scheme_(programming_language) Scheme language
 * @license MIT License
 * @author Ksr
 */
final class SchemeParser
{
    public static SchemeParser $context;
    
    public bool $hasParsed = false;

    protected string $input;
    protected OutputInterface $output;
    protected array $callstack = array();

    protected array $parsedExpressions = array();
    protected array $parsedTerms = array();
    protected array $evaluatedExpressions = array();

    protected array $operations = array();

    public function __construct(string $input, ?OutputInterface $output = NULL)
    {
        $this->input = $input;
        $this->output = $output;

        $this->createDebugLog("Registering available Scheme operations", LogType::SECTION);
        $this->registerOperations();
    }

    /**
     * Parse the input and check integrity of scheme expressions
     * 
     * @return void
     * @author Ksr
     */
    public function parse() : void
    {
        SchemeParser::$context = $this;

        $this->createDebugLog("Start recursive parsing", LogType::SECTION);

        try
        {
            $this->extractExpressions();

            $this->createDebugLog("Building found expressions", LogType::SECTION);

            foreach($this->parsedExpressions as $ex)
            {
                $term = SchemeExpression::parseTerm($ex);
                $term->build();
    
                array_push($this->parsedTerms, $term);
            }
        }
        catch (Exception $ex)
        {
            $this->createErrorLog("Scheme parsing error", "Parsing", $ex->getMessage());
        }

        $this->createDebugLog("parsing done");
        $this->hasParsed = true;
    }

    /**
     * Recursively evaluate the parsed input and print the result in $output
     * 
     * @throws Exception when an evaluation error is met
     * 
     * @return void
     * @author Ksr
     */
    public function evaluate() : void
    {
        if($this->hasParsed == false)
        {
            throw new Exception("cannot evaluate without parsing first");
        }

        SchemeParser::$context = $this;
        $this->createDebugLog("Start recursive evaluation", LogType::SECTION);

        foreach($this->parsedTerms as $term)
        {
            try
            {
                $termEval = $term->evaluate();
                $this->createDebugLog($termEval, LogType::RESULT);
            }
            catch (Exception $ex)
            {
                $this->createErrorLog("Scheme error", "Evaluation", $ex->getMessage());
                $this->popCallstackLog();
            }
        }
    }

    /**
     * Recursively evaluate the parsed input and return the result
     * 
     * @throws Exception when an evaluation error is met
     * 
     * @return string evaluation result
     * @author Ksr
     */
    public function getEvaluation() : string
    {
        if($this->hasParsed == false)
        {
            throw new Exception("cannot evaluate without parsing first");
        }

        SchemeParser::$context = $this;

        $evaluation = "";

        foreach($this->parsedTerms as $term)
        {
            try
            {
                $termEval = $term->evaluate();
                $evaluation = $evaluation."\n".$termEval;
            }
            catch (Exception $ex)
            {
                $this->createErrorLog("\nScheme error", "Evaluation", $ex->getMessage());
            }
        }

        return $evaluation;
    }

    /**
     * Parse the input and fills $parsedExpressions array with found scheme expressions in the $input
     * 
     * @throws Exception if the parsing is not possible
     * 
     * @return void
     * @author Ksr
     */
    protected function extractExpressions() : void
    {
        $index = 0;
        $char = '';
        $tempstr = "";
        
        $this->createDebugLog("extracting expressions from: ".$this->input);

        while($index < strlen($this->input))
        {
            $char = $this->input[$index];

            if($char == '(')
            {
                $expression = SchemeExpression::getExpressionFromIndex($this->input, $index);
                array_push($this->parsedExpressions, $expression);
                $index++;
            }
            else if(ctype_space($char) || $char == "\n" || $char == "\r")
            {
                if(strlen($tempstr) > 0)
                {
                    array_push($this->parsedExpressions, $tempstr);
                    $tempstr = "";
                }

                $index++;
            }
            else
            {
                $tempstr = $tempstr.$char;
                $index++;
            }
        }

        if(strlen($tempstr) > 0)
        {
            array_push($this->parsedExpressions, $tempstr);
        }

        $this->createDebugLog("found expressions: ".var_export($this->parsedExpressions, true));
    }

    /**
     * Fill the $operation array with available scheme operations classes from the $classes array
     * 
     * @return void
     * @author Ksr
     */
    protected function registerOperations() : void
    {
        $classes = array();
        $this->gatherClasses($classes);
        
        foreach($classes as $class)
        {
            $op = new $class();
            $op->checkSettings();
            array_push($this->operations, $op);
        }

        $this->createDebugLog("successfully registered Scheme operations");
    }

    /**
     * Fill the $output array with available scheme operations classes find in project using reflection
     * 
     * @param array &$output to push found classes
     * 
     * @return void
     * @author Ksr
     */
    protected function gatherClasses(array &$output) : void
    {
        $this->createDebugLog("gather subclasses of class \"SchemeOperation\" using reflection");

        foreach(get_declared_classes() as $class)
        {
            if(is_subclass_of($class, __NAMESPACE__."\\Operation\\SchemeOperation"))
            {
                array_push($output, $class);
            }
        }

        $this->createDebugLog("found classes: ".var_export($output, true));
    }

    /**
     * Try to get the operation corresponding to a keyword
     * 
     * @param string $keyword keyword of the seeked operation
     * @param SchemeOperation &$operation will be set to the found operator if found else set to NULL
     * 
     * @return bool true if the operation was found
     * @author Ksr
     */
    public function tryGetOperation(string $keyword, ?SchemeOperation &$operation) : bool
    {
        $hasFoundOperation = false;

        foreach($this->operations as $operation)
        {
            if($operation->keyword == $keyword)
            {
                return true;
            }
        }

        $operation = NULL;
        return $hasFoundOperation;
    }

    /**
     * Add a log to the callstack
     * 
     * @param string $log the log to add
     * 
     * @return void
     * @author Ksr
     */
    public function addLogToCallstack(string $log) : void
    {
        array_push($this->callstack, $log);
    }

    /**
     * Remove the last added log to the callstack
     * 
     * @return void
     * @author Ksr
     */
    public function popCallstackLog() : void
    {
        array_pop($this->callstack);
    }

    /**
     * Create error log from raw error or exception message
     * 
     * @param string $prefix indication on the type of error
     * @param string $stackName name of the stack
     * @param string $err content of the error log
     * 
     * @return void
     * @author Ksr
     */
    protected function createErrorLog(string $prefix, string $stackName, string $err) : void
    {
        $log = $prefix.": ".$err;

        if($this->output->isVerbose() && count($this->callstack) > 0)
        {
            $log = $log."\n----- ".$stackName." Stack -----";
            
            $idx = count($this->callstack) - 1;
            
            while($idx >= 0)
            {
                $log = $log."\n".$idx.": ".$this->callstack[$idx];
                $idx--;
            }
        }

        $this->output->writeln("\n<error>".$log."</error> \n");
    }

    /**
     * Create a debug log. Will print log in direct if parameter $printDebug is true.
     * 
     * @param string $content log content
     * 
     * @return void
     * @author Ksr
     */
    public function createDebugLog(string $content, LogType $type = LogType::INFO) : void
    {
        $logtype = "";
        $indicator = "";

        $shouldPrint = false;

        switch($type)
        {
            case LogType::INFO:
                $logtype = "comment";
                $indicator = "-> ";
                $shouldPrint = ($this->output->isDebug());
                break;
            case LogType::SECTION:
                $logtype = "question";
                $shouldPrint = ($this->output->isDebug());
                break;
            case LogType::RESULT:
                $logtype = "info";
                $indicator = "> ";
                $shouldPrint = true;
            default:
                break;
        }

        if($shouldPrint == false)
        {
            return;
        }

        $indentation = "";
        foreach($this->callstack as $_)
        {
            $idx = 0;
            while($idx < 4)
            {
                $indentation = $indentation." ";
                $idx++;
            }
            $indentation = $indentation."|";
        }

        $content = preg_replace("/\r|\n/", "\n"."</".$logtype.">".$indentation."<".$logtype.">", $content);

        $log = $indentation."<".$logtype.">".$indicator.$content."</".$logtype.">";
        
        $this->output->writeln($log);
    }
}

enum LogType : int
{
    case INFO = 0;
    case SECTION = 1;
    case RESULT = 2;
}
?>
