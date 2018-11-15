<?php

namespace App\Generator;

use App\Generator\Traits\RelationSchemaTrait;

abstract class ModifyGenerator extends Generator
{
    use RelationSchemaTrait;

    public function generateBelong()
    {
        $this->generate();
    }
}
