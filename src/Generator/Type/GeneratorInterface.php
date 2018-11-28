<?php

namespace Sleekcube\AdminGenerator\Generator;


interface GeneratorInterface
{
    const UNFILLABLE = ['id', 'slug', 'locale','created_at', 'updated_at'];

    const IGNORED_INDEXES = ['id', 'locale'];

    const KEY_INJECTION = "INJECT_CODE_HERE_";

    public function setTable($table);

    public function getTable();

    public function generate();
}
