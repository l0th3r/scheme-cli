<?php
namespace Ksr\SchemeCli\Tools\Scheme;

/**
 * Define a scheme language parser and implementing the tools related to interpret the scheme language 
 *
 * @link https://en.wikipedia.org/wiki/Scheme_(programming_language) Scheme language
 * @license MIT License
 * @author Ksr
 */
final class SchemeParser
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

        // parse

        return "";
    }
}
?>
