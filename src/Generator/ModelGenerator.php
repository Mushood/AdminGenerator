<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\StringManipulator;
use Sleekcube\AdminGenerator\Helpers\Pluraliser;
use Illuminate\Support\Facades\Schema;

class ModelGenerator extends CreateGenerator
{
    const SNIPPETS = [
        'topBoilerplate' => "/topBoilerplate.txt",
        'dependencies' => '/dependencies.txt',
        'sluggable' => '/sluggable.txt',
        'translatable' => '/translatable.txt',
        'dependenciesClass' => '/dependenciesClass.txt',
        'slugDependClass' => '/slugDependClass.txt',
        'transDependClass' => '/transDependClass.txt',
        'openingTag' => '/openingTag.txt',
        'routeKeyName' => '/routeKeyName.txt',
        'relationBelong' => '/relationBelong.txt',
    ];

    /**
     * Opening tags and namespace
     *
     * @param $file
     * @return mixed
     */
    protected function writeTopBoilerplate($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['topBoilerplate']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * import any dependencies used in the class
     *
     * @param $file
     * @return mixed
     */
    protected function writeDependencies($file, $translationConfigs = [])
    {
        if (isset($translationConfigs['isTranslatableModel']) && $translationConfigs['isTranslatableModel']) {
            $this->addTranslable($file, $translationConfigs = []);
        } else {
            $this->addSluggable($file, $translationConfigs = []);
        }

        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['dependencies']);
        fwrite($file, $boilerplate);

        return $file;
    }

    private function addTranslable($file)
    {
        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['translatable']);

        fwrite($file, $boilerplate);

        return $file;
    }

    private function addSluggable($file)
    {
        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['sluggable']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * open class syntax
     *
     * @param $file
     * @return mixed
     */
    protected function writeClassOpeningTag($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['openingTag']);

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
        if (isset($translationConfigs['isTranslatableModel']) && $translationConfigs['isTranslatableModel']) {
            $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['transDependClass']);
        } else {
            $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['slugDependClass']);
        }

        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        foreach ($columns as $key => $column) {
            $found = false;
            if (!in_array($column,self::UNFILLABLE)) {
                $boilerplate = StringManipulator::genericReplace($boilerplate, $column);
                $boilerplate = str_replace('entitycolumn', $column, $boilerplate);
                $found = true;
            }

            if ($found) {
                break;
            }
        }

        fwrite($file, $boilerplate);

        return $file;
    }

    protected function customize($file)
    {
        $file = $this->writeFillable($file);
        $file = $this->writeGetRouteKeyName($file);

        return $file;
    }

    protected function customizeBelong($file)
    {
        $file = $this->writeFillable($file);
        $file = $this->writeGetRouteKeyName($file);
        $file = $this->addRelations($file);

        return $file;
    }

    protected function customizeTranslation($file, $translationConfigs = [])
    {
        $file = $this->writeFillable($file);

        if (isset($translationConfigs['isTranslatableModel']) && $translationConfigs['isTranslatableModel']) {
            $file = $this->writeTranslatedAttributes($file);
        } else {
            $file = $this->writeGetRouteKeyName($file);
        }

        $file = $this->addRelations($file);

        return $file;
    }

    /**
     * Write fillable array for values that can be accepted during mass creation
     *
     * @param $file
     * @return mixed
     */
    private function writeFillable($file)
    {
        $boilerplate = "protected $" . "fillable = [";

        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE)) {
                $boilerplate .= "'" . $column . "', ";
            }
        }

        $boilerplate .= "];\n\n";

        fwrite($file, $boilerplate);

        return $file;
    }

    private function writeTranslatedAttributes($file)
    {
        $boilerplate = "protected $" . "translatedAttributes = [";

        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table . "_translation")); dd($columns);
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $column != $this->table . "_id") {
                $boilerplate .= "'" . $column . "', ";
            }
        }

        $boilerplate .= "];\n\n";

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function writeGetRouteKeyName($file)
    {
        $boilerplate = $this->replaceBySnippet('model', self::SNIPPETS['routeKeyName']);

        fwrite($file, $boilerplate);

        return $file;
    }

    public function addRelations($file)
    {
        $indexes = $this->getIndexes();

        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        foreach ($columns as $key => $column) {
            if (!in_array($column,self::IGNORED_INDEXES) && in_array($column,$indexes)) {
                $functionName = $this->getOwnerModelName($column);
                $boilerplate = file_get_contents($this->getRelationPath());
                $boilerplate = str_replace('FUNCTIONNAME', $functionName, $boilerplate);
                $boilerplate = str_replace('MODELNAME', ucwords($functionName), $boilerplate);

                fwrite($file, $boilerplate);

                return $file;
            }
        }
    }

    public function getRelationPath()
    {
        return $this->getSnippetDirectory('model') . self::SNIPPETS['relationBelong'];
    }
}
