<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Exception;

class SchemeParser
{
    protected static mixed $res;

    public static function parse(string $input) : SchemeParserResponse
    {
        if(str_starts_with($input, "\"") && str_ends_with($input, "\""))
        {
            $input = substr($input, 0, -1);
            $input = substr($input, 1);
        }
        $input = preg_replace("/\r|\n/", "", $input);

        $response = new SchemeParserResponse($input);

        try
        {
            SchemeParser::recursiveParsing($response);
        }
        catch(Exception $exc)
        {
            $response->hasError = true;
            $response->error = SchemeParser::formErrorMsg($exc->getMessage());
        }

        return $response;
    }

    protected static function recursiveParsing(SchemeParserResponse &$response)
    {
    }

    protected static function valueConversion(string $value, mixed &$output) : bool
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

    protected static function getNextClosingIndex(string $input) : bool
    {
        $parentOperations = array();
        $parentOperations = [true, false];

        return SchemeParser::isArrayAllTrue($parentOperations);

        for($i = 0; $i < strlen($input); $i++)
        {
            $isParentOpen = $input[$i] == "(";
            $isParentClosed = $input[$i] == ")";

        }

        // get integrity result
        return false;
    }

    protected static function isArrayAllTrue(array $arr) : bool
    {
        foreach($arr as $item)
        {
            if($item == false)
                return false;
        }

        return true;
    }

    protected static function formErrorMsg(string $rawError) : string
    {
        return "SchemeError: $rawError\n";
    }
}
?>
