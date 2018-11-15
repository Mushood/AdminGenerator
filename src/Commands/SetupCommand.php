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

        $source = $currentDirectory . "/../Snippets/middleware/Locale.php";
        $destination = $projectDirectory . "/app/Http/Middleware/Locale.php";
        copy($source,$destination);
    }
}
