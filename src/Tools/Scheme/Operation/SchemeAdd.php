<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

class SchemeAdd extends SchemeOperation
{
    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
        $this->operandMin = 1;
        $this->operandMax = -1;

        $this->checkSettings();
    }

    public function operateEval(array $operands, bool $checkIntegrity = false): string
    {
        $result = 0;

        if($checkIntegrity == true)
        {
            $this->checkIntegrity($operands);
        }

        foreach($operands as $operand)
        {
            if(str_contains($operand, '.'))
            {
                $result += floatval($operand);
            }
            else
            {
                $result += intval($operand);
            }
        }

        return strval($result);
    }
}
?>

