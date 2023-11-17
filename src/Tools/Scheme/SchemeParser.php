<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeArgType;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeExpression;
use Ksr\SchemeCli\Tools\Scheme\Evaluable\SchemeTerm;
use Ksr\SchemeCli\Tools\Scheme\Operation\SchemeAdd;
use Ksr\SchemeCli\Tools\Scheme\Operation\SchemeOperation;

/**
 * Define a scheme language parser and implementing the tools related to interpret the scheme language 
 *
 * @link https://en.wikipedia.org/wiki/Scheme_(programming_language) Scheme language
 * @license MIT License
 * @author Ksr
 */
class SchemeParser
{
    /**
     * Interpret a scheme declaration
     * 
     * @param string $input the unparsed scheme declaration
     *
     * @throws Exception when the interpretation fails
     * @author ksr
     */
    public static function parse(string $input)
    {
        if(str_starts_with($input, "\"") && str_ends_with($input, "\""))
        {
            $input = substr($input, 0, -1);
            $input = substr($input, 1);
        }
        $input = preg_replace("/\r|\n/", "", $input);

        // $expression = new SchemeExpression($input);
        // $expression->build();

        // echo "Recursive print: ".$expression->print()."\n";

        // $expression->evaluate();

        $args = array(
            new SchemeTerm("10", SchemeArgType::NUMERIC),
            new SchemeTerm("10", SchemeArgType::NUMERIC)
        );

        $operator = new SchemeAdd("+");

        $printval = $operator->operateEval($args, true);
        print_r($args);
        echo "result: ".$printval."\n";

        return "";
    }
}
?>
