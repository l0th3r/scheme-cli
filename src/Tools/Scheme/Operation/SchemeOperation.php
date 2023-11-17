<?php
namespace Ksr\SchemeCli\Tools\Scheme\Operation;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;

use Exception;

/**
 * Define a scheme operation used to recursively obtain a result from arguments
 *
 * @license MIT License
 * @author Ksr
 */
abstract class SchemeOperation
{
    protected string $keyword;
    protected int $operandMin;
    protected int $operandMax;
    protected array $operandTypes = array();

    /**
     * Check the settings of the operation
     * 
     * @throws Exception if one or multiple settings are invalid
     * @author ksr
     * @return void
     */     
    protected function checkSettings()
    {
        if(strlen($this->keyword) <= 0)
        {
            throw new Exception("keyword ".$this->keyword." is invalid. From class ".get_class($this)." has invalid keyword");
        }

        if($this->operandMin < 0)
        {
            throw new Exception("operator ".get_class($this)." has a too few minimum operands");
        }
        
        if($this->operandMax == 0 || $this->operandMax < -1)
        {
            throw new Exception("operator ".get_class($this)." has a too few maximum operands, set it to -1 to allow a infinite maximum operands");
        }
    }

    /**
     * Check integrity of operands used with this operator
     * 
     * @param array $operands (type SchemeEvaluable class) operands to evaluate
     * 
     * @throws Exception if integrity is damaged
     * @author ksr
     * @return void
     */ 
    public function checkIntegrity(array $operands) : void
    {
        if($this->operandMax > 0 && count($operands) > $this->operandMax)
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

            if($expected != SchemeArgType::UNDETERMINED && $given != $expected)
            {
                throw new Exception("operand `".$operand."` is type `".$given."` when expected `".$expected."`");
            }
        }
    }

    /**
     * Execute the operation
     * 
     * @param array $operands (type SchemeEvaluable class) operands to evaluate
     * 
     * @param bool $checkIntegrity set to true if integrity must be checked before executing the operation.
     * 
     * @throws Exception if something goes wrong with the operation
     * @author ksr
     * @return string result of the operation
     */ 
    public abstract function operateEval(array $operands, bool $checkIntegrity = false) : string;
}
?>
