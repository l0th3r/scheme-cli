<?php
namespace Ksr\SchemeCli\Tools\Scheme;

class SchemeParserResponse
{
    protected string $input;

    protected $defineDeclarations = array();

    public function __construct($input)
    {
        $this->input = $input;
        $defineDeclarations = [];
    }
}
?>
