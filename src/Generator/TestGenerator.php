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
        file_put_contents($destination, $content);
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
