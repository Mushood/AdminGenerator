<?php

namespace Sleekcube\AdminGenerator\Generator;

interface GeneratorFactoryInterface
{
    public function makeGenerator($type, $table, $directory, $extension);
}
