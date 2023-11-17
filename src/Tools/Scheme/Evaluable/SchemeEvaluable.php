<?php
namespace Ksr\SchemeCli\Tools\Scheme\Evaluable;

/**
 * Define every scheme parsed element that can be recursively evaluated to a result
 *
 * @license MIT License
 * @author Ksr
 */ 
abstract class SchemeEvaluable
{
    public string $input;
    public SchemeArgType $type;

    /**
     * Recursively parse scheme expression or term
     * 
     * @throws Exception if one recursively created scheme expression or term throws parsing error
     * @author ksr
     * @return void
     */ 
    public abstract function build() : void;

    /**
     * Recursively evaluate result of scheme expression or term
     * 
     * @throws Exception if used before scheme expression building
     * @author ksr
     * @return string result of the evaluation
     */ 
    public abstract function evaluate() : string;

    /**
     * Recursively get scheme expression or term
     * 
     * @throws Exception if used before expression building
     * @author ksr
     * @return string scheme expression
     */ 
    public abstract function print() : string;
}
?>
