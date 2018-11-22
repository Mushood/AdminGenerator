<?php

namespace Sleekcube\AdminGenerator\Commands;

use Illuminate\Console\Command;

class SchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sleekcube:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The schedule command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sleekcube Schedule");

        $migrations = config('sleekcube.migrations');

        foreach ($migrations as $table => $type) {
            $command = "generate:" . $type;
            $this->call($command, [
                'table' => $table
            ]);
        }
    }
}
