<?php
namespace Ksr\SchemeCli\Tools\Scheme;

class SchemeParserResponse
{
    public readonly string $input;
    public string $rest;
    public string $result;

    public bool $hasError;
    public string $error;

    public $parsingStack = array();

    public function __construct($input)
    {
        $this->input = $input;
        $this->rest = $input;
        $this->result = "";

        $this->hasError = false;
        $this->error = "";
        
        $parsingStack = [];
    }

    public function addToParsingStack(string $element)
    {
        array_push($parsingStack, $element);
    }
}
?>
