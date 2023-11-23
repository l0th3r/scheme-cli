<?php
namespace Ksr\SchemeCli\Tools\Scheme\Evaluable;

use Ksr\SchemeCli\Tools\Scheme\LogType;
use Ksr\SchemeCli\Tools\Scheme\SchemeParser;

/**
 * Define a scheme term which is also a evaluable scheme element
 *
 * @license MIT License
 * @author Ksr
 */
class SchemeTerm extends SchemeEvaluable
{
    /**
     * Construct SchemeTerm instance
     * 
     * @param string $input the unparsed scheme term
     * 
     * @param SchemeArgType $type the type of the term (cannot be EXPRESSION or UNDETERMINED in this class)
     *
     * @author ksr
     */
    public function __construct(string $input, SchemeArgType $type)
    {
        $this->input = $input;
        $this->type = $type;
    }

    public function build() : void
    {
        SchemeParser::$context->createDebugLog("building term: ".$this->input);
    }

    public function evaluate() : SchemeTerm
    {
        SchemeParser::$context->createDebugLog("evaluating term: ".$this->input);
        return $this;
    }

    public function getEvaluation() : SchemeTerm
    {
        return $this;
    }

    public function print() : string
    {
        return $this->input;
    }
}
?>
