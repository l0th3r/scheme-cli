<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Ksr\SchemeCli\Tools\Scheme\Tree\SchemeExpression;

class SchemeParser
{
    public static function parse(string $input)
    {
        if(str_starts_with($input, "\"") && str_ends_with($input, "\""))
        {
            $input = substr($input, 0, -1);
            $input = substr($input, 1);
        }
        $input = preg_replace("/\r|\n/", "", $input);

        $expression = new SchemeExpression($input);
        $expression->build();

        return "";
    }

    protected static function formErrorMsg(string $rawError) : string
    {
        return "SchemeError: $rawError\n";
    }
}
?>
