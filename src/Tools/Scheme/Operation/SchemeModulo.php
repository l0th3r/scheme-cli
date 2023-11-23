<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;

class SchemeModulo extends SchemeOperation
{
    public function __construct()
    {
        $this->keyword = 'modulo';
        $this->operandMin = 2;
        $this->operandMax = 2;

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

        $operand1 = SchemeOperation::parseNumericValue($operands[0]->input);
        $operand2 = SchemeOperation::parseNumericValue($operands[1]->input);

        $result = $operand1 % $operand2;

        return new SchemeTerm(strval($result), SchemeArgType::NUMERIC);
    }
}
?>

