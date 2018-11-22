<?php

namespace Sleekcube\AdminGenerator;

use Illuminate\Support\ServiceProvider;

class AdminGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\SetupCommand::class,
                Commands\SchedulerCommand::class,
                Commands\GenerateSingular::class,
                Commands\GenerateBelongTo::class,
                Commands\GenerateTranslation::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/Config/sleekcube.php' => config_path('sleekcube.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
