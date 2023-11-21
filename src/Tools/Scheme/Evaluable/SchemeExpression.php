<?php
namespace Ksr\SchemeCli\Tools\Scheme\Evaluable;

use Exception;

/**
 * Define a scheme expression which is also a evaluable scheme element
 * 
 * @param string $input the raw scheme expression to parse
 *
 * @license MIT License
 * @author Ksr
 */
class SchemeExpression extends SchemeEvaluable
{
    public bool $hasBeenBuild = false;

    protected int $index = 0;
    protected string $expression = "";
    protected string $operator = "";
    protected array $rawArgs = array();
    protected array $args = array();

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->type = SchemeArgType::EXPRESSION;
    }

    public function build() : void
    {
        $this->index = 0;
        $this->expression = SchemeExpression::getExpressionFromIndex($this->input, $this->index);
        $this->operator = SchemeExpression::getOperator($this->expression, $this->index);
        
        SchemeExpression::getArgs($this->expression, $this->index, $this->rawArgs);
        SchemeExpression::parseArgs($this->rawArgs, $this->args);

        $this->hasBeenBuild = true;
    }

    public function evaluate() : string
    {
        if($this->hasBeenBuild == false)
            throw new Exception("Cannot evaluate unbuild expression");

        // TODO EVALUATION OF EXPRESSION
        return "EVAL";
    }

    public function print() : string
    {
        if($this->hasBeenBuild == false)
        {
            throw new Exception("Cannot print unbuild expression");
        }

        $out = "(";

        $out = $out.$this->operator." ";

        for($i = 0; $i < count($this->args); $i++)
        {
            $out = $out.$this->args[$i]->print();

            if($i + 1 < count($this->args))
                $out = $out." ";
        }

        $out = $out.")";

        return $out;
    }

    /**
     * Get the scheme expression from index
     * (Will not check the integrity of the scheme input)
     * 
     * @param string $input source to extract scheme expressions
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, it will be incremented after the end of function
     * 
     * @author ksr
     * @return string the found expression
     */ 
    public static function getExpressionFromIndex(string $input, int &$index) : string
    {
        $validator = array();

        $expression = "";

        while($index < strlen($input))
        {
            $c = $input[$index];

            if($c == '(')
            {
                array_push($validator, true);
            }
            else if($c == ')')
            {
                array_pop($validator);
            }

            $expression = $expression.$c;

            if(count($validator) <= 0)
                break;
            
            $index++;
        }

        return $expression;
    }

    /**
     * Get the operator in scheme expression
     * (Will not check the integrity of the scheme expression)
     *
     * @param string $expression scheme expression to extract operator
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, the value of $index can be impacted
     * 
     * @author ksr
     * @return string the found operator
     */ 
    public static function getOperator(string $expression, int &$index) : string
    {
        $index = 1;
        $operator = "";

        do
        {
            $c = $expression[$index++];
            $operator = $operator.$c;
        }
        while(ctype_alpha($c) && $index < strlen($expression));

        //TODO FIX SPACE BEFORE OPERATOR

        return $operator;
    }

    /**
     * Get the arguments in scheme expression
     * (Will not check the integrity of the scheme expression)
     *
     * @param string $expression scheme expression to extract arguments from
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, the value of $index can be impacted
     * @param array &$output array to add each parsed scheme expression argument
     * as it is a reference, the value of $output can be impacted 
     * 
     * @author ksr
     * @return void
     */ 
    public static function getArgs(string $expression, int &$index, array &$output) : void
    {        
        while($index < strlen($expression))
        {
            $c = $expression[$index];

            if($c == '(')
            {
                array_push($output, SchemeExpression::getExpressionFromIndex($expression, $index));
            }
            else if(ctype_alnum($c))
            {
                $arg = "";
                
                do
                {
                    $arg = $arg.$c;
                    $c = $expression[++$index];
                } while(ctype_alnum($c) && $index < strlen($expression));
                
                $index--;
                array_push($output, $arg);
            }

            $index++;
        }
    }

    /**
     * Recursively parse raw scheme arguments into scheme evaluable
     * 
     * @param array $rawArgs array of string arguments to parse to evaluable
     * @param array $outputArgs array of SchemeEvaluable to push parsed arguments
     * as it is a reference, the value of $outputArgs can be impacted
     * 
     * @throws Exception when a parsing error is met
     * @author ksr
     * @return void
     */ 
    public static function parseArgs(array $rawArgs, array &$outputArgs) : void
    {
        foreach($rawArgs as $arg)
        {
            array_push($outputArgs, SchemeExpression::parseTerm($arg));
        }

        foreach($outputArgs as $parsedArg)
        {
            $parsedArg->build();
        }
    }

    /**
     * Parse one scheme expression term into a scheme evaluable
     *
     * @param string $arg raw string argument to parse
     * 
     * @author ksr
     * @return SchemeEvaluable generated with parsing the argument
     */ 
    public static function parseTerm(string $arg) : SchemeEvaluable
    {
        if (is_numeric($arg))
        {
            return new SchemeTerm($arg, SchemeArgType::NUMERIC);
        }
        else if(str_starts_with($arg, "(") && str_ends_with($arg, ")"))
        {
            return new SchemeExpression($arg);
        }
        else if(str_starts_with($arg, '\'') || str_starts_with($arg, '"') && str_ends_with($arg, '"'))
        {
            return new SchemeTerm($arg, SchemeArgType::STRING);
        }
        else
        {
            throw new Exception("unknown identifier: ".$arg);
        }

        // TODO add check of define function aliases
    }
}
?>