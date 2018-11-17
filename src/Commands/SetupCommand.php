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

    protected $currentDirectory;

    protected $projectDirectory;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->currentDirectory = dirname(__FILE__);
        $this->projectDirectory = getcwd();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sleekcube Setup Beginning");

        /*$this->copyMiddleware();
        $this->createModelsDirectory();
        $this->createTransformerDirectory();
        $this->createRequestDirectory();
        $this->copyAssets();
        $this->copyLibrary();
        $this->copyControllers();
        $this->copyValidators();
        $this->copyModels();
        $this->copyTransformers();
        $this->copyMigrations();*/
        $this->copyViews();
        $this->copyRoutes();

        $this->info("Sleekcube Setup Done");
    }

    private function copyMiddleware()
    {
        $source = $this->currentDirectory . "/../Snippets/middleware/Locale.php";
        $destination = $this->projectDirectory . "/app/Http/Middleware/Locale.php";
        copy($source,$destination);

        $source = $this->currentDirectory . "/../Snippets/middleware/Admin.php";
        $destination = $this->projectDirectory . "/app/Http/Middleware/Admin.php";
        copy($source,$destination);
    }

    private function createModelsDirectory()
    {
        $modelDirectory = $this->projectDirectory . "/app/Models";
        if (!is_dir($modelDirectory)) {
            mkdir($modelDirectory);
        }

        $source = $this->projectDirectory . "/app/User.php";
        $destination = $modelDirectory . "/User.php";
        $file = file_get_contents($source, true);
        $renameNamespace = explode("namespace App;", $file);
        $renameNamespace = $renameNamespace[0] . "namespace App\Models;" . $renameNamespace[1];
        copy($source,$destination);
        file_put_contents($destination, $renameNamespace);
        unlink($source);
    }

    private function createTransformerDirectory()
    {
        $transformerDirectory = $this->projectDirectory . "/app/Transformers";
        if (!is_dir($transformerDirectory)) {
            mkdir($transformerDirectory);
        }
    }

    private function createRequestDirectory()
    {
        $requestDirectory = $this->projectDirectory . "/app/Http/Requests";
        if (!is_dir($requestDirectory)) {
            mkdir($requestDirectory);
        }
    }

    private function copyAssets()
    {
        $source = $this->currentDirectory . "/../Snippets/assets";
        $destination = $this->projectDirectory . "/resources/assets";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyLibrary()
    {
        $source = $this->currentDirectory . "/../Library";
        $destination = $this->projectDirectory . "/app/Library";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyControllers()
    {
        $source = $this->currentDirectory . "/../Controllers/*";
        $destination = $this->projectDirectory . "/app/Http/Controllers";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyValidators()
    {
        $source = $this->currentDirectory . "/../Requests";
        $destination = $this->projectDirectory . "/app/Http/Requests";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyModels()
    {
        $source = $this->currentDirectory . "/../Models/*";
        $destination = $this->projectDirectory . "/app/Models";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyTransformers()
    {
        $source = $this->currentDirectory . "/../Transformers/*";
        $destination = $this->projectDirectory . "/app/Transformers";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyMigrations()
    {
        $source = $this->currentDirectory . "/../Migrations/*";
        $destination = $this->projectDirectory . "/database/migrations";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyViews()
    {
        $source = $this->currentDirectory . "/../Views/*";
        $destination = $this->projectDirectory . "/resources/views";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }

    private function copyRoutes()
    {
        $source = $this->currentDirectory . "/../Routes/*";
        $destination = $this->projectDirectory . "/routes";
        $command = "cp -R " . $source . " " . $destination;
        exec($command);
    }
}
