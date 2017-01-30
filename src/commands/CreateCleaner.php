<?php

namespace LaravelLaundromat\commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Console\AppNamespaceDetectorTrait;

class CreateCleaner extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laundromat:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new cleaner class.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDirectory();

        $namespace = $this->getAppNamespace().'Cleaners';

        $name = $this->argument('name');

        $cleanerName = $namespace.'\\'.$name;

        if (class_exists($cleanerName)) {
            $this->error("Cleaner {$name} already exists!");

            return;
        }

        $this->writeCleaner($name, $namespace);

        $this->info("Cleaner {$name} was successfully created!");
    }

    /**
     * Create Cleaners directory if it doesn't exist.
     */
    protected function createDirectory()
    {
        if (!File::exists(app_path('Cleaners'))) {
            $this->info('Creating Cleaners directory...');

            File::makeDirectory(app_path('Cleaners'));
        }
    }

    /**
     * Write the property bag file into the settings folder.
     *
     * @param string $namespace
     */
    protected function writeCleaner($name, $namespace)
    {
        $cleaner = file_get_contents(
            __DIR__.'/../Stub.php'
        );

        $cleaner = str_replace('{{namespace}}', $namespace, $cleaner);

        $cleaner = str_replace('{{className}}', $name, $cleaner);

        file_put_contents(app_path("Cleaners/{$name}.php"), $cleaner);
    }
}
