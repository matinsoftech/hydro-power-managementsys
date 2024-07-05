<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {traitName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Trait File';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $traitName = $this->argument('traitName');
        $traitFileName = Str::studly($traitName) . '.php';
        $traitFilePath = app_path("Traits/{$traitFileName}");

        if (File::exists($traitFilePath)) {
            $this->error("Trait {$traitFileName} already exists!");
            return;
        }

        File::put($traitFilePath, $this->getTraitContent($traitName));

        $this->info("Trait {$traitFileName} created successfully.");
    }

    protected function getTraitContent($traitName)
    {
        $namespace = 'App\Traits';

        return "<?php\n\nnamespace {$namespace};\n\ntrait {$traitName}\n{\n    // Trait code goes here\n}\n";
    }

}
