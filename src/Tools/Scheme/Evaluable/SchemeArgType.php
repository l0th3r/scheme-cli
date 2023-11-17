<?php
namespace Ksr\SchemeCli\Tools\Scheme\Evaluable;

/**
 * Used to define the type of argument for a scheme operation
 *
 * @license MIT License
 * @author Ksr
 */ 
enum SchemeArgType : int
{
    case UNDETERMINED = 1;
    case EXPRESSION = 2;
    case STRING = 4;
    case NUMERIC = 8;
}
?>
