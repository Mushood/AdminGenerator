<?php

namespace App\Generator;

interface GeneratorFactoryInterface
{
    public function makeGenerator($type, $table, $directory, $extension);
}
