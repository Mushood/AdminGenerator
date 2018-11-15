<?php

namespace App\Generator;


interface GeneratorInterface
{
    const UNFILLABLE = ['id', 'slug', 'locale','created_at', 'updated_at'];

    const IGNORED_INDEXES = ['id', 'locale'];

    public function setTable($table);

    public function getTable();

    public function generate();
}
