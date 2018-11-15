<?php

namespace Sleekcube\AdminGenerator\Commands;

use Sleekcube\AdminGenerator\Generator\GeneratorFactory;
use Illuminate\Console\Command;

class GenerateTranslation extends Command
{
    const FILES_TO_GENERATE = [
        'model' => [
            'directory' => '/app/Models/',
            'extension' => '.php',
            'double'    => true,
        ],
        'request' => [
            'directory' => '/app/Http/Requests/',
            'extension' => 'Validator.php',
            'double'    => false,
        ],
        'transformer' => [
            'directory' => '/app/Transformers/',
            'extension' => 'Transformer.php',
            'double'    => false,
        ],
        'controller' => [
            'directory' => '/app/Http/Controllers/',
            'extension' => 'Controller.php',
            'double'    => false,
        ],
        'view' => [
            'directory' => '/resources/assets/components/pages/',
            'extension' => '.vue',
            'double'    => false,
        ],
        'route' => [
            'directory' => '/routes/',
            'extension' => 'api.php',
            'double'    => false,
        ],
        'route_js' => [
            'directory' => '/resources/assets/',
            'extension' => '',
            'double'    => false,
        ],
    ];
    protected $generatorFactory;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:translation {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse a table with its translation table to create admin interface';

    /**
     * GenerateSingular constructor.
     * @param GeneratorFactory $generatorFactory
     */
    public function __construct(GeneratorFactory $generatorFactory)
    {
        parent::__construct();

        $this->generatorFactory = $generatorFactory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $table = $this->argument('table');

        foreach (self::FILES_TO_GENERATE as $type => $file) {
            $this->info('Creating ' . $type);
            $translationConfigs = [
                'isTranslation' => true,
                'isTranslatedModel' => false,
                'isTranslatableModel' => true,
            ];
            $generator = $this->generatorFactory->makeGenerator($type,$table, $file['directory'], $file['extension']);
            $generator->generateTranslation($translationConfigs);
            $this->info($type .' File Created');

            if ($file['double']) {
                $this->info('Creating Translation' . $type);
                $translationConfigs = [
                    'isTranslation' => true,
                    'isTranslatedModel' => true,
                    'isTranslatableModel' => false,
                ];
                $generator->setTable($table . "_translation");
                $generator->generateTranslation($translationConfigs);
                $this->info($type .' Translation File Created');
            }
        }

        //create test
    }
}
