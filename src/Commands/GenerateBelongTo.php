<?php

namespace App\Console\Commands;

use App\Generator\GeneratorFactory;
use Illuminate\Console\Command;

class GenerateBelongTo extends Command
{
    const FILES_TO_GENERATE = [
        'model' => [
            'directory' => '/app/Models/',
            'extension' => '.php'
        ],
        'request' => [
            'directory' => '/app/Http/Requests/',
            'extension' => 'Validator.php'
        ],
        'transformer' => [
            'directory' => '/app/Transformers/',
            'extension' => 'Transformer.php'
        ],
        'controller' => [
            'directory' => '/app/Http/Controllers/',
            'extension' => 'Controller.php'
        ],
        'view' => [
            'directory' => '/resources/assets/components/pages/',
            'extension' => '.vue'
        ],
        'route' => [
            'directory' => '/routes/',
            'extension' => 'api.php'
        ],
        'route_js' => [
            'directory' => '/resources/assets/',
            'extension' => ''
        ],
    ];
    protected $generatorFactory;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:belongto {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse a table with belongTo relationship to create admin interface';

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
            $generator = $this->generatorFactory->makeGenerator($type,$table, $file['directory'], $file['extension']);
            $generator->generateBelong();
            $this->info($type .' File Created');
        }

        //create test
    }
}
