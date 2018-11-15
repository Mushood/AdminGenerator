<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\StringManipulator;
use Sleekcube\AdminGenerator\Helpers\Pluraliser;
use Illuminate\Support\Facades\File;

class ViewGenerator extends ModifyGenerator
{
    const KEY_INJECTION = "INJECT_CODE_HERE_";
    const SNIPPETS = [
        'string' => "/string.txt",
        'text' => '/text.txt',
        'integer' => '/integer.txt',
        'selectrel' => '/selectrel.txt',
        'relload' => '/relload.txt',
        'validate' => '/validate.txt',
        'validateText' => '/validateText.txt',
    ];
    /**
     * Generate Model Class File
     */
    public function generate()
    {
        $path = base_path() . $this->directory . $this->table;
        if (!\File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $this->createListFile();
        $this->createListTableHeadView();
        $this->createListTableBodyView();

        $this->createCreateFile();
        $this->createForm();
        $this->createData();
        $this->createOriginal();
        $this->createResetFunction();
    }

    public function generateBelong()
    {
        $this->generate();
        $this->addLoadingFunctions();
        $this->addToMountedFunction();
        $this->addToData();
    }

    public function generateTranslation($translationConfigs = [])
    {
        $path = base_path() . $this->directory . $this->table;
        if (!\File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $this->createListFileTranslation();
        $this->createListTableHeadView($translationConfigs);
        $this->createListTableBodyView($translationConfigs);

        $this->createCreateFileTranslation();
        $this->createForm($translationConfigs);
        $this->createFormTranslation($translationConfigs);
        $this->createData();
        $this->createOriginal();
        $this->createTranslation();
        $this->createResetFunction();
        $this->addLoadingFunctions();
        $this->addToMountedFunction();
        $this->addToData();
    }

    /**
     * Create File
     *
     * @return bool|resource
     */
    private function createListFile()
    {
        $source = base_path() . '/snippets/view/index.vue';
        $destination = $this->getListFilePath();

        copy($source, $destination);

        $content = file_get_contents($destination);
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    private function createListFileTranslation()
    {
        $source = base_path() . '/snippets/view/indexTranslation.vue';
        $destination = $this->getListFilePath();

        copy($source, $destination);

        $content = file_get_contents($destination);
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    /**
     * Create Table Head
     */
    private function createListTableHeadView($translationConfigs = [])
    {
        $destination = $this->getListFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'1', $content);
        $content[0] .= "<thead>\n<tr>\n<th scope='col'>#</th>\n";

        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        }

        foreach ($columns as $key => $column) {
            $found = false;
            if (!in_array($column,self::UNFILLABLE)) {
                $content[0] .= "<th scope='col'>" . ucwords($column) ."</th>\n";
                $found = true;
            }

            if ($found) {
                break;
            }
        }
        $content[0] .= "<th scope='col'>Action</th>\n";
        $content[0] .= "</tr>\n</thead>\n";
        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    /**
     * Create Table List View
     */
    private function createListTableBodyView($translationConfigs = [])
    {
        $destination = $this->getListFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'2', $content);
        $content[0] .= "<td scope='row'>{{ index+1 }}</td>\n";


        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        }

        foreach ($columns as $key => $column) {
            $found = false;
            if (!in_array($column,self::UNFILLABLE)) {
                $content[0] .= "<td>{{ " . $this->table . "." . $column . " }}</td>\n";
                $found = true;
            }

            if ($found) {
                break;
            }
        }

        $content[0] .= "<td>\n<i class='fa fa-pencil text-info mr-3' @click='editEntitygenerator(entitygenerator)'></i>\n";
        $content[0] .= "<i class='fa fa-trash text-danger' @click='deleteEntitygenerator(entitygenerator)'></i>\n";
        $content[0] .= "</td>\n";
        $content = $content[0] . $content[1];

        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    /**
     * Get File Path
     *
     * @return string
     */
    public function getListFilePath()
    {
        $filename = 'index' . $this->extension;

        return base_path() . $this->directory . $this->table . '/' .  $filename;
    }

    /**
     * Create File
     *
     * @return bool|resource
     */
    private function createCreateFile()
    {
        $source = base_path() . '/snippets/view/create.vue';
        $destination = $this->getCreateFilePath();

        copy($source, $destination);

        $content = file_get_contents($destination);
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    private function createCreateFileTranslation()
    {
        $source = base_path() . '/snippets/view/createTranslation.vue';
        $destination = $this->getCreateFilePath();

        copy($source, $destination);

        $content = file_get_contents($destination);
        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    /**
     * @return string
     */
    public function getCreateFilePath()
    {
        $filename = 'create' . $this->extension;

        return base_path() . $this->directory . $this->table . '/' .  $filename;
    }

    /**
     * Create Form Vue JS Block
     */
    public function createForm($translationConfigs = [])
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'1', $content);

        $indexes = $this->getIndexes();
        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {
                $content[0] .= "<div class='row_input'>\n";

                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] = $this->addRelationField($column, $content[0]);
                } else {
                    $content[0] .= "<label for='" . $column . "' class='control-label'>" . $column . ":</label>\n";
                    $content[0] = $this->addField($column, $content[0]);
                }

                $content[0] .= "</div>\n";
            }
        }
        $content[0] .= "\n";

        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $content[0] .= "\n INJECT_CODE_HERE_1";
        }

        $content = $content[0] . $content[1];

        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    public function createFormTranslation($translationConfigs = [])
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'1', $content);

        $indexes = $this->getIndexes($this->table . '_translation');
        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table . '_translation'));
        $translationConfigs['isTranslatedModel'] = true;
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                $content[0] .= "<div class='row_input'>\n";

                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] = $this->addRelationField($column, $content[0], $translationConfigs);
                } else {
                    $content[0] .= "<label for='" . $column . "' class='control-label'>" . $column . ":</label>\n";
                    $content[0] = $this->addField($column, $content[0], $translationConfigs);
                }

                $content[0] .= "</div>\n";
            }
        }
        $content[0] .= "\n";
        $content = $content[0] . $content[1];

        $content = StringManipulator::genericReplace($content, $this->table);
        file_put_contents($destination, $content);
    }

    private function addField($column, $content, $translationConfigs = [])
    {
        if (isset($translationConfigs['isTranslatedModel']) && $translationConfigs['isTranslatedModel']) {
            $type =  \Schema::getColumnType(Pluraliser::getPlural($this->table . "_translation"), $column);
            $isNotNull =  \Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($this->table . "_translation"), $column)->getNotnull();
        } else {
            $type =  \Schema::getColumnType(Pluraliser::getPlural($this->table), $column);
            $isNotNull =  \Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($this->table), $column)->getNotnull();
        }

        switch ($type) {
            case "string":
                $content .= $this->replaceBySnippet('view', self::SNIPPETS['string']);
                $content = str_replace('entitycolumn', $column, $content);

                if ($isNotNull){
                    $content .= $this->replaceBySnippet('view', self::SNIPPETS['validate']);
                    $content = str_replace('entitycolumn', $column, $content);
                } else {
                    $content .= "/>\n";
                }

                break;

            case "text":
                $content .= $this->replaceBySnippet('view', self::SNIPPETS['text']);
                $content = str_replace('entitycolumn', $column, $content);

                if ($isNotNull){
                    $content .= $this->replaceBySnippet('view', self::SNIPPETS['validateText']);
                    $content = str_replace('entitycolumn', $column, $content);
                }

                break;

            case "integer":
                $content .= $this->replaceBySnippet('view', self::SNIPPETS['integer']);
                $content = str_replace('entitycolumn', $column, $content);

                if ($isNotNull){
                    $content .= $this->replaceBySnippet('view', self::SNIPPETS['validateText']);
                    $content = str_replace('entitycolumn', $column, $content);
                }

                break;
        }

        return $content;
    }

    private function addRelationField($column, $content, $translationConfigs = [])
    {
        if (isset($translationConfigs['isTranslatedModel']) && $translationConfigs['isTranslatedModel']) {
            $isNotNull =  \Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($this->table . "_translation"), $column)->getNotnull();
        } else {
            $isNotNull =  \Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($this->table), $column)->getNotnull();
        }

        $name = $this->getOwnerModelName($column);

        $content .= "<label for='" . $name . "' class='control-label'>" . ucwords($name) . ":</label>\n";

        $content .= $this->replaceBySnippet('view', self::SNIPPETS['selectrel']);
        $content = str_replace('entitycolumn', $name, $content);
        $content = str_replace('tablename', $this->table, $content);

        if ($isNotNull){
            $content .= " v-validate=\"'required'\" >";
        } else {
            $content .= ">\n";
        }

        $content .= "\n<option :value=\"" . $name . ".slug\" v-for=\"" . $name . " in " . Pluraliser::getPlural($name) .
            "\">{{ " . $name . ".title }}</option>\n</select>\n";

        if ($isNotNull){
            $content .= "<span v-show=\"errors.has('" . $name . "')\" class=\"help is-danger\">{{ errors.first('" .
                $name . "') }}</span>";
        }

        return $content;
    }

    /**
     * Create Data Block
     */
    public function createData()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'2', $content);

        $content[0] .= $this->table . ": {\n";
        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        $indexes = $this->getIndexes();
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= $this->getOwnerModelName($column) . " : undefined,\n";
                } else {
                    $content[0] .= $column . " : undefined,\n";
                }
            }
        }
        $content[0] .= "},\n";

        $content[0] .= "original: {\n";
        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= $this->getOwnerModelName($column) . " : undefined,\n";
                } else {
                    $content[0] .= $column . " : undefined,\n";
                }
            }
        }
        $content[0] .= "},";

        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    /**
     * Create Original data block
     */
    public function createOriginal()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'3', $content);

        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        $indexes = $this->getIndexes();
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= "vm.original." . $this->getOwnerModelName($column) . " = response.data.data." . $this->getOwnerModelName($column) . ";\n";
                    $content[0] .= "vm." . $this->table . "." . $this->getOwnerModelName($column) . " = vm." . $this->table . "." . $this->getOwnerModelName($column) . ".slug;\n";
                } else {
                    $content[0] .= "vm.original." . $column . " = response.data.data." . $column . ";\n";
                }
            }
        }


        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    /**
     * Create translation reset
     */
    /**
     * Create Original data block
     */
    public function createTranslation()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'8', $content);

        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table . "_translation"));
        $indexes = $this->getIndexes($this->table . "_translation");
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= "vm." . $this->table . "." . $this->getOwnerModelName($column) . " = undefined;\n";
                } else {
                    $content[0] .= "vm." . $this->table . "." . $column . " = undefined;\n";
                }
            }
        }

        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    /**
     * Add Reset Function
     */
    public function createResetFunction()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION.'4', $content);

        $columns = \Schema::getColumnListing(Pluraliser::getPlural($this->table));
        $indexes = $this->getIndexes();
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {

                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $content[0] .= "this.entitygenerator." . $this->getOwnerModelName($column) . " = this.original." . $this->getOwnerModelName($column) . ";\n";
                } else {
                    $content[0] .= "this.entitygenerator." . $column . " = this.original." . $column . ";\n";
                }
            }
        }
        $content[0] = str_replace("entitygenerator", $this->table, $content[0]);
        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    public function addLoadingFunctions()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode("//" . self::KEY_INJECTION . '5', $content);

        $indexes = $this->getIndexes();
        $relationFunction =  $this->replaceBySnippet('view', self::SNIPPETS['relload']);
        foreach ($indexes as $key => $index) {
            if (!in_array($index,self::IGNORED_INDEXES)) {
                $content[0] .= $relationFunction;
            }
        }
        $content[0] = str_replace("Ownermodels", Pluraliser::getPlural((ucwords($this->getOwnerModelName($index)))), $content[0]);
        $content[0] = str_replace("ownermodels", Pluraliser::getPlural($this->getOwnerModelName($index)), $content[0]);
        $content[0] = str_replace("ownermodel", $this->getOwnerModelName($index), $content[0]);
        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    public function addToMountedFunction()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode("//" . self::KEY_INJECTION . '6', $content);

        $indexes = $this->getIndexes();
        foreach ($indexes as $key => $index) {
            if (!in_array($index,self::IGNORED_INDEXES)) {
                $content[0] .= "vm.getOwnermodels();\n" . "//" . self::KEY_INJECTION . "6";
            }
        }
        $content[0] = str_replace("Ownermodels", Pluraliser::getPlural((ucwords($this->getOwnerModelName($index)))), $content[0]);
        $content = $content[0] . $content[1];
        file_put_contents($destination, $content);
    }

    public function addToData()
    {
        $destination = $this->getCreateFilePath();
        $content = file_get_contents($destination);
        $content = explode("//" . self::KEY_INJECTION . '7', $content);

        $indexes = $this->getIndexes();
        foreach ($indexes as $key => $index) {
            if (!in_array($index,self::IGNORED_INDEXES)) {
                $content[0] .= "ownermodels: [],\n" . "//" . self::KEY_INJECTION . "7";
            }
        }
        $content[0] = str_replace("ownermodels", Pluraliser::getPlural($this->getOwnerModelName($index)), $content[0]);
        $content = $content[0] . $content[1];
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
