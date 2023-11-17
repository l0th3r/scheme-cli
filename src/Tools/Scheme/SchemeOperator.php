<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;

abstract class SchemeOperator
{
    protected int $operandMin;
    protected int $operandMax;
    protected array $operandTypes = array();

    /**
     * Check integrity of operands used with this operator
     * 
     * @param array &$operands (type SchemeEvaluable class) operands used with operator
     * 
     * @throws Exception if integrity is damaged
     * @author ksr
     * @return void
     */ 
    public function checkIntegrity(array &$operands) : void
    {
        if(count($operands) > $this->operandMax)
        {
            throw new Exception("too much operands");
        }
        
        if(count($operands) < $this->operandMin)
        {
            throw new Exception("too few operands");
        }

        for($i = 0; $i < count($operands); $i++)
        {
            $operand = $operands[$i]->input;
            $expected = $this->operandTypes[$i];
            $given = $operands[$i]->type;

            if($expected != SchemeArgType::NONE && $given != $expected)
            {
                throw new Exception("operand `".$operand."` is type `".$given."` when expected `".$expected."`");
            }
        }
    }
}
?>
