<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = str_replace('\\', '/', $this->argument('name'));

        $path = app_path('Services/' . $name . '.php');

        if (File::exists($path)) {
            $this->error('Service already exists.');

            return Command::FAILURE;
        }

        File::ensureDirectoryExists(
            dirname($path)
        );

        $parts = explode('/', $name);

        $className = array_pop($parts);

        $namespace = 'App\\Services';

        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        $content = "<?php\n\n"
            . "namespace {$namespace};\n\n"
            . "class {$className}\n"
            . "{\n"
            . "    //\n"
            . "}\n";

        File::put($path, $content);

        $this->info(
            "Service {$name} created successfully."
        );

        return Command::SUCCESS;
    }
}