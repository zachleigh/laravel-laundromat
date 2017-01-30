<?php

namespace LaravelLaundromat;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class LaundromatServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        Collection::macro('clean', function ($class = null) {
            return $this->map(function (Model $item) use ($class) {
                return $item->clean($class);
            });
        });

        $this->registerCommands();
    }

    /**
     * Register the package commands.
     */
    protected function registerCommands()
    {
        $this->app->singleton('command.laundromat.create', function ($app) {
            return $app['LaravelLaundromat\commands\CreateCleaner'];
        });

        $this->commands('command.laundromat.create');
    }
}
