<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;

class SchemeMultiply extends SchemeOperation
{
    public function __construct()
    {
        $this->keyword = '*';
        $this->operandMin = 1;
        $this->operandMax = -1;

        $this->operandTypes = array
        (
            SchemeArgType::NUMERIC->value
        );

        $this->checkSettings();
    }

    public function operateEval(array $operands, bool $checkIntegrity = false): string
    {
        $result = 0;

        if($checkIntegrity == true)
        {
            $this->checkIntegrity($operands);
        }

        foreach($operands as $term)
        {
            $operand = $term->input;

            if(str_contains($operand, '.'))
            {
                $result *= floatval($operand);
            }
            else
            {
                $result *= intval($operand);
            }
        }

        return strval($result);
    }
}
?>

