<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\StringManipulator;
use Sleekcube\AdminGenerator\Helpers\Pluraliser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class TestGenerator extends ModifyGenerator
{
    /**
     * Generate Model Class File
     */
    public function generate()
    {
        $this->copyTestFile();
        $this->addData();
        $this->addDeleteData();
        $this->addValidationTest();
    }

    public function generateBelong()
    {

    }

    public function generateTranslation($translationConfigs = [])
    {

    }

    /**
     * Create File
     *
     * @return bool|resource
     */
    private function copyTestFile()
    {
        $source = dirname(__FILE__) . '/../Tests/SimpleTest.php';
        $destination = base_path() . $this->directory . StringManipulator::pascalize($this->table) . "Test.php";

        copy($source, $destination);

        $content = file_get_contents($destination);
        $content = StringManipulator::genericReplace($content, $this->table);
        $content = str_replace("entitytable", Pluraliser::getPlural($this->table), $content);
        file_put_contents($destination, $content);
    }

    private function addData($translationConfigs = [])
    {
        $destination = base_path() . $this->directory . StringManipulator::pascalize($this->table) . "Test.php";
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'1', $content);

        $indexes = $this->getIndexes($this->table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        }

        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= "'" . $this->table . "." . $this->getOwnerModelName($column) . "' => '";
                } else {
                    $content[0] .= "'" . $column . "' => $" . "this->faker->";
                }

                $content[0] = $this->addFaker($content[0], $column, $this->table);

                $content[0] .= ", \n";
            }
        }

        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    private function addFaker($boilerplate, $column, $table)
    {
        switch (Schema::getColumnType(Pluraliser::getPlural($table), $column)) {
            case "string":
                $boilerplate .= "text(15)";
                break;

            case "text":
                $boilerplate .= "text(50)";
                break;

            case "boolean":
                $boilerplate .= "boolean";
                break;

            case "datetime":
                $boilerplate .= "datetime";
                break;
        }

        return $boilerplate;
    }

    private function addDeleteData($translationConfigs = [])
    {
        $destination = base_path() . $this->directory . StringManipulator::pascalize($this->table) . "Test.php";
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'2', $content);

        $indexes = $this->getIndexes($this->table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        }

        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= "'" . $this->table . "." . $this->getOwnerModelName($column) . "' => '";
                } else {
                    $content[0] .= "$" . "this->data['" . $column . "'] = $" . "this->entitygenerator->" . $column;
                }

                $content[0] .= "; \n";
            }
        }

        $content = $content[0] . $content[1];
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    private function addValidationTest($translationConfigs = [])
    {
        $source = dirname(__FILE__) . '/../Snippets/test';
        $required = file_get_contents($source . '/required.txt');
        $nullable = file_get_contents($source . '/nullable.txt');

        $destination = base_path() . $this->directory . StringManipulator::pascalize($this->table) . "Test.php";
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'3', $content);

        $indexes = $this->getIndexes($this->table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        }

        foreach ($columns as $key => $column) {
            if (Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($this->table), $column)->getNotnull()) {
                if (!in_array($column, self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                    if (in_array($column, $indexes) && !in_array($column, self::IGNORED_INDEXES)) {
                        $content[0] .= "";
                    } else {
                        $content[0] .= $this->replace($required, $column);
                    }

                    $content[0] .= "\n";
                }
            } else {
                if (!in_array($column, self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                    if (in_array($column, $indexes) && !in_array($column, self::IGNORED_INDEXES)) {
                        $content[0] .= "";
                    } else {
                        $content[0] .= $this->replace($nullable, $column);
                    }

                    $content[0] .= "\n ";
                }
            }
        }

        $content = $content[0] . $content[1];
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    private function replace($content, $column)
    {
        $content = StringManipulator::genericReplace($content, $this->table);
        $content = str_replace("Column", ucwords($column), $content);
        $content = str_replace("column", $column, $content);

        return $content;
    }

    /**
     * Setter
     *
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Getter
     *
     * @return String
     */
    public function getTable()
    {
        return $this->table;
    }
}
