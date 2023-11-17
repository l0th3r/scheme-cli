<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;

class SchemeExpression extends SchemeEvaluable
{
    public bool $hasBeenBuild = false;

    protected int $index = 0;
    protected string $expression = "";
    protected string $operator = "";
    protected array $rawArgs = array();
    protected array $args = array();

    /**
     * Construct SchemeExpression instance
     * 
     * @param string $input the unparsed scheme expression
     *
     * @author ksr
     */ 
    public function __construct(string $input)
    {
        $this->input = $input;
        $this->type = SchemeArgType::EXPRESSION;
    }

    public function build() : void
    {
        $this->index = 0;
        $this->expression = $this->getExpressionFromIndex($this->index);
        $this->operator = $this->getOperator($this->index);
        $this->getArgs($this->index, $this->rawArgs);
        $this->parseArgs();

        // echo "Expression: ".$this->expression."\n";
        // echo "Operator: ".$this->operator."\n";
        // echo "Args: "; print_r($this->rawArgs);

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

    /**
     * Get the scheme expression from index
     *
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, it will be incremented after the end of function
     * 
     * @author ksr
     * @return string the found expression
     */ 
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

    /**
     * Get the operator in scheme expression
     *
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, the value of $index can be impacted
     * 
     * @author ksr
     * @return string the found operator
     */ 
    protected function getOperator(int &$index) : string
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

    /**
     * Get the arguments in scheme expression
     *
     * @param int &$index index where to start to parse scheme expression
     * as it is a reference, the value of $index can be impacted
     * 
     * @param array &$output array to add each parsed scheme expression argument
     * as it is a reference, the value of $output can be impacted 
     * 
     * @author ksr
     * @return void
     */ 
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

    /**
     * Parse all the scheme expression's arguments
     * 
     * @author ksr
     * @return void
     */ 
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

    /**
     * Parse one scheme expression's argument
     *
     * @param string $arg raw argument to parse
     * 
     * @author ksr
     * @return SchemeEvaluable generated with parsing the argument
     */ 
    protected function parseArg(string $arg) : SchemeEvaluable
    {
        if (is_numeric($arg))
        {
            return new SchemeTerm($arg, SchemeArgType::NUMERIC);
        }
        else if(str_starts_with($arg, "(") && str_ends_with($arg, ")"))
        {
            return new SchemeExpression($arg);
        }
        else
            return new SchemeTerm($arg, SchemeArgType::STRING);
    }
}
?>
