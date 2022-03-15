<?php

namespace EscolaSoft\LaravelH5p\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'laravel-h5p:install';

    protected $description = 'Builds part of the file structure for Laravel-H5P as well as necessary symlinks.';

    function handle() {
        $this->line('');
        $this->info('Creating Folders...');

        try {
            mkdir(storage_path('/app/public/h5p'), 0777);
            mkdir(storage_path('/app/public/h5p/exports'), 0777);
            mkdir(storage_path('/app/public/h5p/libraries'), 0777);
        } catch (\Throwable $th) {
            $this->warn('Could not create folders.');
            $this->line($th);
        }


        $this->info('Creating Symlinks...');
        try {
            symlink(storage_path('/app/public/h5p/exports'), public_path('/assets/vendor/h5p/exports'));
            symlink(storage_path('/app/public/h5p/libraries'), public_path('/assets/vendor/h5p/libraries'));
        } catch (\Throwable $th) {
            $this->warn("Could not create symlinks, please check the readme for further instructions.");
        }
    }
}
