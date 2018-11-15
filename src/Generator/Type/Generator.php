<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\StringManipulator;

abstract class Generator implements GeneratorInterface
{
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

    abstract public function generate();

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
        return base_path() . "/snippets/" .  $type;
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
