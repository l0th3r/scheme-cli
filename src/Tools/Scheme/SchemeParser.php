<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Operation\SchemeOperation;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeExpression;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;

// TODO FIX THE NEED TO REQUIRE TO SEARCH AMONG CLASSES
require_once __DIR__.'/Operation/SchemeAdd.php';
require_once __DIR__.'/Operation/SchemeMultiply.php';
require_once __DIR__.'/Operation/SchemeDivide.php';

/**
 * Scheme language parser, provide a scheme interpretation context
 *
 * @link https://en.wikipedia.org/wiki/Scheme_(programming_language) Scheme language
 * @license MIT License
 * @author Ksr
 */
final class SchemeParser
{
    public string $input;
    public static SchemeParser $context;

    public bool $returnErrors;
    public bool $returnCallstack;

    protected array $callstack = array();

    protected array $parsedExpressions = array();
    protected array $parsedTerms = array();
    protected array $evaluatedExpressions = array();

    protected array $operations = array();

    public function __construct(string $input, bool $returnErrors = true, bool $returnCallstack = true)
    {
        $this->input = $input;
        $this->returnErrors = $returnErrors;
        $this->returnCallstack = $returnCallstack;

        $this->registerOperations();
    }

    /**
     * Parse the input and check integrity of scheme expressions
     * 
     * @return string parsing and evaluation result
     * @author Ksr
     */
    public function parse() : string
    {
        SchemeParser::$context = $this;

        try
        {
            $this->extractExpressions();

            foreach($this->parsedExpressions as $ex)
            {
                $term = SchemeExpression::parseTerm($ex);
                $term->build();
    
                array_push($this->parsedTerms, $term);
            }
        }
        catch (Exception $ex)
        {
            $log = new SchemeTerm(
                $this->createErrorLog("Scheme parsing error", "Parsing", $ex->getMessage()),
                SchemeArgType::STRING
            );
            
            array_push($this->parsedTerms, $log);
        }

        return $this->evaluate();
    }

    /**
     * Recursively evaluate the parsed input
     * 
     * @throws Exception when an evaluation error is met
     * 
     * @return string evaluation result
     * @author Ksr
     */
    protected function evaluate() : string
    {
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
                $evaluation = $evaluation."\n\n".$this->createErrorLog(
                    "Scheme error",
                    "Evaluation",
                    $ex->getMessage()
                );
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
        foreach(get_declared_classes() as $class)
        {
            if(is_subclass_of($class, __NAMESPACE__."\\Operation\\SchemeOperation"))
            {
                array_push($output, $class);
            }
        }
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
     * @return string formatted error log
     * @author Ksr
     */
    protected function createErrorLog(string $prefix, string $stackName, string $err) : string
    {
        if($this->returnErrors == false)
        {
            return "";
        }

        $log = $prefix.": ".$err;

        if($this->returnCallstack)
        {
            $log = $log."\n----- ".$stackName." Stack -----";
            
            $idx = count($this->callstack) - 1;
            
            while($idx >= 0)
            {
                $log = $log."\n".$idx.": ".$this->callstack[$idx];
                $idx--;
            }
        }

        return "<error>".$log."</error>\n";
    }
}
?>
