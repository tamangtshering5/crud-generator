<?php
namespace Pemba\Crud;
use Illuminate\Support\ServiceProvider;
use Pemba\Crud\Commands\CreateController;
use Pemba\Crud\Commands\CreateMigration;
use Pemba\Crud\Commands\CreateModel;
use Pemba\Crud\Commands\CreateModule;

class CrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/crud.php', 'crud'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->publishes([
            __DIR__.'/../config/crud.php' => config_path('crud.php')
        ], 'crud-config');
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateModule::class,
                CreateModel::class,
                CreateMigration::class,
                CreateController::class,
            ]);
        }
    }

}
