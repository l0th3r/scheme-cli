<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;

class SchemeSubstract extends SchemeOperation
{
    public function __construct()
    {
        $this->keyword = '-';
        $this->operandMin = 1;
        $this->operandMax = -1;

        $this->operandTypes = array
        (
            SchemeArgType::NUMERIC->value
        );

        $this->checkSettings();
    }

    public function operateEval(array $operands, bool $checkIntegrity = false): SchemeTerm
    {
        $result = 0;

        if($checkIntegrity == true)
        {
            $this->checkIntegrity($operands);
        }

        if(count($operands) == 1)
        {
            $operand = $operands[0]->input;
            $value = 1;
            
            $value = SchemeOperation::parseNumericValue($operand);

            $result = -$value;
        }
        else
        {
            for($i = 0; $i < count($operands); $i++)
            {
                $operand = $operands[$i]->input;

                if($i == 0)
                {
                    $result = SchemeOperation::parseNumericValue($operand);
                }
                else
                {
                    $result -= SchemeOperation::parseNumericValue($operand);
                }
            }
        }

        return new SchemeTerm(strval($result), SchemeArgType::NUMERIC);
    }
}
?>