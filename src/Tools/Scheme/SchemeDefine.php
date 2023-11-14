<?php
namespace Ksr\SchemeCli\Tools\Scheme;

final class SchemeDefine
{
    public string $arg1;
    public string $arg2;

    public function __construct(string $arg1, string $arg2)
    {
        $this->$arg1 = $arg1;
        $this->$arg2 = $arg2;
    }
}
?>
