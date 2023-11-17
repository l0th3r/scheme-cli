<?php
namespace Ksr\SchemeCli\Tools\Scheme;

class SchemeTerm implements SchemeEvaluable
{
    public readonly string $input;
    public readonly SchemeTermType $type;

    protected string $term = "";

    public function __construct(string $input, SchemeTermType $type)
    {
        $this->input = $input;
        $this->type = $type;
    }

    public function build() : void
    {
        // build term maybe depending on type
        $this->term = $this->input;
    }

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
