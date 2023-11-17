<?php
namespace Ksr\SchemeCli\Tools\Scheme\Evaluable;

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

    public function build() : void {}

    public function evaluate() : string
    {
        return $this->input;
    }

    public function print() : string
    {
        return $this->input;
    }
}
?>
