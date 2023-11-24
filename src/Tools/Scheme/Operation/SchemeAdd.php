<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;

class SchemeAdd extends SchemeOperation
{
    public function __construct()
    {
        $this->keyword = '+';
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

        foreach($operands as $term)
        {
            $result += SchemeOperation::parseNumericValue($term->input);
        }

        return new SchemeTerm(strval($result), SchemeArgType::NUMERIC);
    }
}
?>