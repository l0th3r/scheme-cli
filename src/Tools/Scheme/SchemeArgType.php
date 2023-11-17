<?php
namespace Ksr\SchemeCli\Tools\Scheme;

/**
 * Used to define the type of argument for a scheme operation
 *
 * @license MIT License
 * @author Ksr
 */ 
enum SchemeArgType
{
    case NUMERIC;
    case EXPRESSION;
    case STRING;
    case UNDETERMINED; // Use as comparison only
}
?>
