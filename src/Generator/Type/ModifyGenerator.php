<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Generator\Traits\RelationSchemaTrait;

abstract class ModifyGenerator extends Generator
{
    use RelationSchemaTrait;

    public function generateBelong()
    {
        $this->generate();
    }
}
