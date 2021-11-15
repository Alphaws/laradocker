<?php
/**
 *
 */

declare(strict_types=1);

namespace Alphaws\Laradocker;

use Illuminate\Support\ServiceProvider;
use Alphaws\Laradocker\Console\InstallCommand;

/**
 * Class LaradockerServiceProvider
 * @package Alphaws\Laradocker
 */
class LaradockerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

        /**
     * Register the Invoices Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
