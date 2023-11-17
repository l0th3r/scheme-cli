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
    protected array $operandTypes;

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

        if(count($this->operandTypes) <= 0)
        {
            throw new Exception("operator ".get_class($this)." has too few operand types. Has defined ".count($this->operandTypes)." need 1");
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

        $otCount = count($this->operandTypes);

        for($i = 0; $i < count($operands); $i++)
        {
            $opTypeIndex = 0;  
            
            if($i >= $otCount)
                $opTypeIndex = $otCount - 1;
            else
                $opTypeIndex = $i;

            $expected = $this->operandTypes[$opTypeIndex];

            $operand = $operands[$i]->input;
            $given = $operands[$i]->type;

            if (SchemeOperation::isTypeIncluded(SchemeArgType::UNDETERMINED->value, $expected) == false
                && SchemeOperation::isTypeIncluded($expected->value, $given->value))
            {
                throw new Exception("operand `".$operand."` is type `".$given->name."` when expected `".$expected->name."`");
            }
        }
    }

    /**
     * Bitwise check if given SchemeArgType is expected in the operation
     * 
     * @param int (bitwise) $expected is the SchemeArgType expected by the operation
     * 
     * @param int (bitwise) $given is the SchemeArgType given to the operation
     *
     * @author ksr
     * @return bool if the given SchemeArgType in included or equal to the expected SchemeArgType
     */ 
    public static function isTypeIncluded(int $expected, int $given) : bool
    {
        return ($expected & $given) == $given;
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
