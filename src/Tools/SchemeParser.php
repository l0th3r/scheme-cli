<?php
namespace Ksr\SchemeCli\Tools;

class SchemeParser
{
    public static function parse(string $input)
    {
        $response = new SchemeParserResponse($input);
        return $response;
    }

    public static function valueConversion(string $value, mixed &$output) : bool
    {
        if (is_numeric($value))
        {
            $output = floatval($value);
        }
        else if (str_starts_with($value, "'"))
        {
            $output = explode("'", $value);
        }
        else
        {
            return false;
        }

        return true;
    }
}
?>
