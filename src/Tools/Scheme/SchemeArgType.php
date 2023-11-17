<?php
namespace Ksr\SchemeCli\Tools\Scheme;

enum SchemeArgType
{
    case NUMERIC;
    case EXPRESSION;
    case STRING;
    case UNDETERMINED; // Use as comparison only
}
?>
