<?php
namespace Ksr\SchemeCli\Tools\Scheme;

use Ksr\SchemeCli\Tools\Scheme\Operation\SchemeOperation;

// TODO FIX THE NEED TO REQUIRE TO SEARCH AMONG CLASSES
require __DIR__.'/Operation/SchemeOperation.php';
require __DIR__.'/Operation/SchemeAdd.php';
require __DIR__.'/Operation/SchemeMultiply.php';

/**
 * Scheme language parser, provide a scheme interpretation context
 *
 * @link https://en.wikipedia.org/wiki/Scheme_(programming_language) Scheme language
 * @license MIT License
 * @author Ksr
 */
final class SchemeParser
{
    protected array $classes = array();
    protected array $operations = array();

    public function __construct()
    {
        $this->gatherClasses();
        $this->registerOperations();

        $this->tryGetOperation("+", $op);

        var_dump($op);
    }

    /**
     * Fill the $operation array with available scheme operations classes from the $classes array
     * 
     * @return void
     * @author Ksr
     */
    protected function registerOperations() : void
    {
        foreach($this->classes as $class)
        {
            $op = new $class();
            $op->checkSettings();
            array_push($this->operations, $op);
        }
    }

    /**
     * Fill the $classes array with available scheme operations classes find in project
     * 
     * @return void
     * @author Ksr
     */
    protected function gatherClasses() : void
    {        
        foreach(get_declared_classes() as $class)
        {
            if(is_subclass_of($class, __NAMESPACE__."\\Operation\\SchemeOperation"))
            {
                array_push($this->classes, $class);
            }
        }
    }

    /**
     * Try to get the operation corresponding to a keyword
     * 
     * @param string $keyword keyword of the seeked operation
     * @param SchemeOperation &$operation will be set to the found operator if found else set to NULL
     * 
     * @return bool true if the operation was found
     * @author Ksr
     */
    protected function tryGetOperation(string $keyword, ?SchemeOperation &$operation) : bool
    {
        $hasFoundOperation = false;

        foreach($this->operations as $operation)
        {
            if($operation->keyword == $keyword)
            {
                return true;
            }
        }

        $operation = NULL;
        return $hasFoundOperation;
    }
}
?>
