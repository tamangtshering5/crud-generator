<?php

namespace Pemba\Crud\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CreateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:model {name : name of model} {--migration} {module : name of module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates model for module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $path;
    private $log;

    public function __construct(LoggerInterface $log)
    {
        parent::__construct();
        $this->path = config('crud.path') . '/';
        $this->log = $log;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelName=$this->argument('name');
        $moduleName=$this->argument('module');
        $migration = $this->option('migration');
        try {
            $this->info("================ Creating Model ======================\n");
//                create model
            $this->model($modelName,$moduleName);

//                create migration
            if ($migration) {
                $this->info("================ Creating Migration ======================\n");
                $this->migration($modelName,$moduleName);
                $this->info("================ Created Migration ======================\n");
            }

            $this->info("================ Model Created Successfully ==========\n");

        }catch (\Exception $e){
            $this->log->error((string) $e);

            $this->error("================ Couldn't Create Model. ======================");
        }
    }

    protected function getStub($type)
    {
        return file_get_contents(dirname(__FILE__) . "/Stubs/$type.stub");
    }

    protected function model($name,$module){
        $modelTemplate = str_replace(
            [
                '{{modelNameCamelCase}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                getCamelCaseName($name),
                getCamelCaseName($module),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('ModelPlain')
        );

        $file = createFile($this->path . $module . '/Models/', getCamelCaseName($name));
        return file_put_contents($file, $modelTemplate);
    }

    protected function migration($name,$module)
    {
        $template = str_replace(
            [
                '{{modelName}}',
                '{{model_snake_case}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                get_plural_snake_case_name($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Migration')
        );

        $date = date('Y_m_d_Hms');

        file_put_contents($this->path . $module . '/Database/migrations/' . $date.'_create_'.get_plural_snake_case_name($name).'_table.php', $template);
    }
}
