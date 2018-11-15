<?php

namespace Sleekcube\AdminGenerator\Generator\Traits;

use App\Helpers\Pluraliser;

trait RelationSchemaTrait
{

    /**
     * @return array
     */
    protected function getIndexes($table = null)
    {
        if ($table === null) {
            $table = $this->table;
        }

        $dbTable = \Schema::getConnection()->getTablePrefix(). Pluraliser::getPlural($table);
        $tableDetails = \Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($dbTable);
        $tableIndexes = $tableDetails->getIndexes();
        $indexNames = [];
        foreach ($tableIndexes as $index) {
            array_push($indexNames, $index->getColumns()[0]);
        }

        return $indexNames;
    }

    /**
     * @param $column
     * @return string
     */
    protected function getOwnerModelName($column)
    {
        $name = explode("_", $column);
        $functionName = $name[0];

        for ($i = 1; $i < (count($name)-1); $i++) {
            $functionName .= ucwords($name[$i]);
        }

        return $functionName;
    }
}
