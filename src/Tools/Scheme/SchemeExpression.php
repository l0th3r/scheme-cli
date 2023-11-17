<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;

class SchemeExpression implements SchemeEvaluable
{
    public readonly string $input;
    public bool $hasBeenBuild = false;

    protected int $index = 0;
    protected string $expression = "";
    protected string $operator = "";
    protected array $rawArgs = array();
    protected array $args = array();

    public function __construct(string $input)
    {
        $this->input = $input;
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
            throw new Exception("Cannot print unbuild expression");

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

    public function build() : void
    {
        $this->index = 0;
        $this->expression = $this->getExpressionFromIndex($this->index);
        $this->operator = $this->getRawOperator($this->index);
        $this->getArgs($this->index, $this->rawArgs);
        $this->parseArgs();

        $this->hasBeenBuild = true;
    }

    protected function getExpressionFromIndex(int &$index) : string
    {
        $validator = array();

        $expression = "";

        while($index < strlen($this->input))
        {
            $c = $this->input[$index];

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

    protected function getRawOperator(int &$index) : string
    {
        $index = 1;
        $operator = "";

        do
        {
            $c = $this->expression[$index++];
            $operator = $operator.$c;
        }
        while(ctype_alpha($c) && $index < strlen($this->expression));

        //TODO FIX SPACE BEFORE OPERATOR

        return $operator;
    }

    protected function getArgs(int &$index, array &$output) : void
    {        
        while($index < strlen($this->expression))
        {
            $c = $this->expression[$index];

            if($c == '(')
            {
                array_push($output, $this->getExpressionFromIndex($index));
            }
            else if(ctype_alnum($c))
            {
                $arg = "";
                
                do
                {
                    $arg = $arg.$c;
                    $c = $this->expression[++$index];
                } while(ctype_alnum($c) && $index < strlen($this->expression));
                
                $index--;
                array_push($output, $arg);
            }

            $index++;
        }
    }

    protected function parseArgs() : void
    {
        foreach($this->rawArgs as $arg)
        {
            array_push($this->args, $this->parseArg($arg));
        }

        foreach($this->args as $parsedArg)
        {
            $parsedArg->build();
        }
    }

    protected function parseArg(string $arg) : mixed
    {
        if (is_numeric($arg))
        {
            return new SchemeTerm($arg, SchemeTermType::NUMERIC);
        }
        else if(str_starts_with($arg, "(") && str_ends_with($arg, ")"))
        {
            return new SchemeExpression($arg);
        }
        else
            return new SchemeTerm($arg, SchemeTermType::STRING);
    }
}
?>
