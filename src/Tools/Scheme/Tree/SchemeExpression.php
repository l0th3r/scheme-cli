<?php
namespace Ksr\SchemeCli\Tools\Scheme\Tree;

class SchemeExpression
{
    public string $input = "";

    protected int $index = 0;
    protected string $expression = "";
    protected string $operator = "";

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function build()
    {
        $this->index = 0;
        $this->expression = $this->getExpressionFromIndex($this->index);
        $this->operator = $this->getOperator();
        echo $this->operator."\n";
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

    protected function getNextElement(int &$index, string &$element) : bool
    {
        $startidx = $index;   
        $c = $this->input[$index];
        
        while($c == ' ' || $c == '\n' || $c == '\r')
        {            
            $c = $this->input[$index];

            $index++;

            if($index == strlen($this->input))
            {
                $index = $startidx;
                return false;
            }
        }

        $index--;
        $element = "";

        while($c != ' ' && $c != '\n' && $c != '\r')
        {            
            $c = $this->input[$index];

            if($c != ' ' && $c != '\n' && $c != '\r')
                $element = $element.$c;

            $index++;

            if($index == strlen($this->input))
            {
                $index = $startidx;
                return false;
            }
        }


        return true;
    }

    protected function getOperator() : string
    {
        return "O";
    }
}
?>
