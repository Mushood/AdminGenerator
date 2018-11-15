<?php

namespace Sleekcube\AdminGenerator\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sleekcube:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The setup command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sleekcube Setup Beginning");

        $currentDirectory = dirname(__FILE__);
        $projectDirectory = getcwd();

        //copy locale middleware
        $source = $currentDirectory . "/../Snippets/middleware/Locale.php";
        $destination = $projectDirectory . "/app/Http/Middleware/Locale.php";
        copy($source,$destination);

        //make models
        $modelDirectory = $projectDirectory . "/app/Models";
        if (!is_dir($modelDirectory)) {
            mkdir($modelDirectory);
        }

        $source = $projectDirectory . "/app/User.php";
        $destination = $modelDirectory . "/User.php";
        $file = file_get_contents($source, true);
        $renameNamespace = explode("namespace App;", $file);
        $renameNamespace = $renameNamespace[0] . "namespace App\Models;" . $renameNamespace[1];
        copy($source,$destination);
        file_put_contents($destination, $renameNamespace);
        unlink($source);

        //make transformers
        $transformerDirectory = $projectDirectory . "/app/Transformers";
        if (!is_dir($transformerDirectory)) {
            mkdir($transformerDirectory);
        }

        $this->info("Sleekcube Setup Done");
    }
}
