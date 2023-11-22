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

    protected array $parsedExpressions = array();
    protected array $parsedTerms = array();
    protected array $evaluatedExpressions = array();
    protected array $operations = array();
    protected bool $hasBeenParsed = false;

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
            $log = new SchemeTerm($this->formErrorLog("Scheme parsing error", $ex->getMessage()), SchemeArgType::STRING);
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
                $evaluation = $evaluation."\n\n".$termEval;
            }
            catch (Exception $ex)
            {
                $evaluation = $evaluation."\n\n".$this->formErrorLog("Scheme error", $ex->getMessage());
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


    protected function formErrorLog(string $prefix, string $err) : string
    {
        if($this->returnErrors == false)
        {
            return "";
        }

        $log = $prefix." -> ".$err;

        if($this->returnCallstack)
        {
            $log = $log."\n----- Stack -----";
            // TODO add callstack
        }

        return "<error>".$log."</error>";
    }
}
?>
