<?php
namespace Ksr\SchemeCli\Tools\Scheme\Tree;

class SchemeExpression
{
    public string $input = "";

    protected int $index = 0;
    protected string $expression = "";
    protected string $operator = "";
    protected array $args = array();

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function build()
    {
        $this->index = 0;
        $this->expression = $this->getExpressionFromIndex($this->index);
        $this->operator = $this->getRawOperator($this->index);
        $this->getArgs($this->index, $this->args);
        
        echo "Expression: `".$this->expression."`\n";
        echo "Operator: `".$this->operator."`\n";
        echo "Args: "; print_r($this->args);
    }

    protected function getExpressionFromIndex(int &$index) : string
    {  
        $c = $this->input[$index];

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

            $dump = "";

            foreach($validator as $item)
            {
                if ($item == true)
                    $item = "true";
                else
                    $item = "false";

                $dump = $dump.$item." ";
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
                while(ctype_alnum($c) && $index < strlen($this->expression))
                {
                    $c = $this->expression[$index++];

                    $arg = $arg.$c;
                }
                $index--;
                array_push($output, $arg);
            }

            $index++;
        }
    }
}
?>
