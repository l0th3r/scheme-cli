<?php
namespace Ksr\SchemeCli\Tools\Scheme;

interface SchemeEvaluable
{
    public function build() : void;
    public function evaluate() : string;
    public function print() : string;
}
?>
