<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Generator\Traits\RelationSchemaTrait;
use Sleekcube\AdminGenerator\Helpers\StringManipulator;
use Sleekcube\AdminGenerator\Helpers\Pluraliser;

abstract class CreateGenerator implements GeneratorInterface
{
    use RelationSchemaTrait;

    /**
     * @var String
     */
    protected $table;

    /**
     * @var String
     */
    protected $directory;

    /**
     * @var String
     */
    protected $extension;

    /**
     * CreateGenerator constructor.
     * @param String $table
     * @param String $directory
     * @param String $extension
     */
    public function __construct(String $table, String $directory, String $extension)
    {
        $this->table = $table;
        $this->directory = $directory;
        $this->extension = $extension;
    }

    /**
     * Generate Model Class File
     */
    public function generate()
    {
        $file = $this->setup();
        $file = $this->customize($file);
        $success = $this->cleanUp($file);

        return $success;
    }

    /**
     * Generate Model Belong Class File
     */
    public function generateBelong()
    {
        $file = $this->setup();
        $file = $this->customizeBelong($file);
        $success = $this->cleanUp($file);

        return $success;
    }

    /**
     * Generate Model Translation Class File
     */
    public function generateTranslation(array $translationConfigs = [])
    {
        $file = $this->setup($translationConfigs);
        $file = $this->customizeTranslation($file, $translationConfigs);
        $success = $this->cleanUp($file, $translationConfigs);

        return $success;
    }

    /**
     * @return bool|mixed|resource
     */
    protected function setup($translationConfigs = [])
    {
        $file = $this->createFile($translationConfigs);
        $file = $this->writeTopBoilerplate($file, $translationConfigs);
        $file = $this->writeDependencies($file, $translationConfigs);
        $file = $this->writeClassOpeningTag($file, $translationConfigs);
        $file = $this->writeClassDependencies($file, $translationConfigs);

        return $file;
    }

    /**
     * @return bool
     */
    protected function cleanUp($file, $translationConfigs = [])
    {
        $file = $this->writeClassClosingTag($file);
        fclose($file);
        $path = dirname(__FILE__) . "/../../../";
        exec('php ' . $path . 'php-cs-fixer-v2.phar fix ' . $this->getFilePath($translationConfigs));

        return true;
    }

    /**
     * Custom logic to write to file
     *
     * @param $file
     * @return mixed
     */
    abstract protected function customize($file);

    /**
     * Opening tags and namespace
     *
     * @param $file
     * @return mixed
     */
    abstract protected function writeTopBoilerplate($file, $translationConfigs);

    /**
     * import any dependencies used in the class
     *
     * @param $file
     * @return mixed
     */
    abstract protected function writeDependencies($file, $translationConfigs);

    /**
     * open class syntax
     *
     * @param $file
     * @return mixed
     */
    abstract protected function writeClassOpeningTag($file, $translationConfigs);

    /**
     * Create File
     *
     * @return bool|resource
     */
    private function createFile($translationConfigs = [])
    {
        $path = $this->getFilePath($translationConfigs);

        return fopen($path, "w");
    }

    /**
     * @return string
     */
    public function getFilePath($translationConfigs = [])
    {
        if (isset($translationConfigs['isTranslatedModel']) && $translationConfigs['isTranslatedModel']) {
            $filename = StringManipulator::pascalize($this->table) . $this->extension;
        } else {
            $filename = ucwords($this->table) . $this->extension;
        }

        return base_path() . $this->directory .  $filename;
    }

    /**
     * close class
     *
     * @param $file
     * @return mixed
     */
    protected function writeClassClosingTag($file)
    {
        $boilerplate = "\n}";

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * Write dependencies in class as well as associated functions
     *
     * @param $file
     * @return mixed
     */
    protected function writeClassDependencies($file, $translationConfigs = [])
    {
        $boilerplate = "";

        fwrite($file, $boilerplate);

        return $file;
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

    /**
     * @param $type
     * @return string
     */
    public function getSnippetDirectory($type)
    {
        return dirname(__FILE__) . "/../../Snippets/" .  $type;
    }

    /**
     * @param $type
     * @param $filename
     * @return bool|mixed|string
     */
    public function replaceBySnippet($type, $filename)
    {
        $directory = $this->getSnippetDirectory($type);
        $filePath = $directory . $filename;
        $boilerplate = StringManipulator::fileReplace($filePath,$this->table);

        return $boilerplate;
    }
}
