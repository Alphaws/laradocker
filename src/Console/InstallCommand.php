<?php
/**
 * This file is part of the LaraStart project.
 *
 * LICENSE: This source file is subject to version 3.14 of the PrStart license
 * that is available through the world-wide-web at the following URI:
 * https://www.prstart.co.uk/license/  If you did not receive a copy of
 * the PrStart License and are unable to obtain it through the web, please
 * send a note to imre@prstart.co.uk, so we can mail you a copy immediately.
 *
 * DESCRIPTION: LaraStart
 *
 * @category   Laravel
 * @package    LaraStart
 * @author     Imre Szeness <imre@prstart.co.uk>
 * @copyright  Copyright (c) 2021 PrStart Ltd. (https://www.prstart.co.uk)
 * @license    https://www.prstart.co.uk/license/ PrStart Ltd. License
 * @version    1.0.0 (11/11/2021)
 * @link       https://www.prstart.co.uk/laravel-development/lara-start/
 * @since      File available since Release 1.0.0
 */

declare(strict_types=1);

namespace Alphaws\Laradocker\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class InstallCommand
 * @package Alphaws\Laradocker\Console
 */
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradocker:install
                            {--name=Laravel : Name of project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init project for Docker';

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
     * @return bool
     */
    public function handle(): bool
    {
        if ('Laravel' == config('app.name')) {
            $this->error('Please change APP_NAME in .env file to an unique name !');
            return false;
        }
        $url = preg_replace("(^https?://)", "", config('app.url') );
        $this->initDockerCompose($url);
        $this->initNginx();
        $this->initDatabase();
        $this->initCommands();
        $this->info('Docker initialized successfully for project: <fg=yellow>' . config('app.name') . '</>');
        $this->info('Check url: `<fg=yellow>' . $url . '</>` and change if you want in .env file and docker-compose.yml file, then run `<fg=yellow>docker-compose up -d</>` command');
        return true;
    }

    /**
     *
     */
    protected function initDockerCompose($url)
    {
        $target = base_path('docker-compose.yml');
        (new Filesystem)->copy(__DIR__ . '/../../resources/stubs/docker-compose.stub', $target);
        $str = file_get_contents($target);
        $str = str_replace(['{{ APP_NAME }}', '{{ APP_URL }}'], [strtolower(config('app.name')), $url], $str);
        file_put_contents($target, $str);
    }

    /**
     *
     */
    protected function initNginx()
    {
        (new Filesystem)->ensureDirectoryExists(base_path('.docker/nginx/conf.d/'));
        $target = base_path('.docker/nginx/conf.d/default.conf');
        (new Filesystem)->copy(__DIR__ . '/../../resources/stubs/.docker/nginx/conf.d/default.conf', $target);
        $str = file_get_contents($target);
        $str = str_replace(['{{ PHP-NAME }}'], [strtolower(config('app.name')).'-php'], $str);
        file_put_contents($target, $str);
    }

    /**
     *
     */
    protected function initDatabase()
    {
        (new Filesystem)->ensureDirectoryExists(base_path('.docker/mariadb/data/'));
        (new Filesystem)->ensureDirectoryExists(base_path('.docker/mariadb/init/'));
        // copy init script

        // replace DB_HOST in .env
    }

    /**
     *
     */
    protected function initCommands()
    {
        (new Filesystem)->ensureDirectoryExists(base_path('bin/'));
        $target = base_path('bin/cli');
        (new Filesystem)->copy(__DIR__ . '/../../resources/stubs/bin/cli', $target);
        $str = file_get_contents($target);
        $str = str_replace(['{{ PHP-NAME }}'], [strtolower(config('app.name')).'-php'], $str);
        file_put_contents($target, $str);
        chmod($target, 0755);

        $files = ['artisan', 'npm', 'wipe', 'composer'];
        foreach ($files as $file) {
            (new Filesystem)->copy(__DIR__ . '/../../resources/stubs/bin/' . $file, base_path('bin/' . $file));
            chmod(base_path('bin/' . $file), 0755);
        }
    }
}
