<?php


namespace Pemba\Crud\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CreateModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:module {name : name of module} {--migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates module';

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
        $this->path=config('crud.path').'/';
        $this->log=$log;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $moduleName=$this->argument('name');
        $migration = $this->option('migration');
        $folders=config('crud.folders');
        $files=config('crud.files');
        try {
            if(!file_exists($this->path . $moduleName)){
                $this->info("================ Creating Package ======================\n");
                //create folder
                if (!empty($folders)) {
                    foreach ($folders as $folder)
                        createFolder($this->path . $moduleName . '/', $folder);
                }

                //create files
                if (!empty($files)) {
                    foreach ($files as $file)
                        createFile($this->path . $moduleName . '/', $file);
                }
//                create model
                $this->model($moduleName);

//                create migration
                if ($migration) {
                    $this->migration($moduleName);
                }

//                create controller
                $this->controller($moduleName);

//                create request
                $this->request($moduleName);

//                create policy
                $this->policy($moduleName);

                //create service provider
                $this->serviceProvider($moduleName);

//                create routes
                $this->routes($moduleName);

//                create blade files
                $this->createBlade($moduleName);

                //creates composer.json file
                system('composer init --working-dir=' . $this->path . $moduleName . ' --name=' . strtolower(getCamelCaseName($moduleName)) . '/' . strtolower(getCamelCaseName($moduleName)) . ' --description=' . getCamelCaseName($moduleName) . '\' package\' --type=library --license=MIT --author=\'Author <info@author.com>\' -s dev');

                $this->info("================ Module Created Successfully ==========\n");

            }
        }catch (\Exception $e){
            $this->log->error((string) $e);

            $this->error("================ Couldn't Create Module. ======================");
        }
    }

    protected function getStub($type)
    {
        return file_get_contents(dirname(__FILE__) . "/Stubs/$type.stub");
    }

    protected function serviceProvider($name)
    {
        $providerTemplate = str_replace(
            [
                '{{moduleName}}',
                '{{moduleNameCamelCase}}',
                '{{moduleNamePluralLowerCase}}',
                '{{moduleNamePluralUpperCase}}',
                '{{moduleNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('ModuleServiceProvider')
        );
        $file = createFile($this->path . $name . '/Providers/', getCamelCaseName($name) . 'ServiceProvider');
        return file_put_contents($file, $providerTemplate);
    }

    protected function model($name){
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Model')
        );

        $file = createFile($this->path . $name . '/Models/', getCamelCaseName($name));
        return file_put_contents($file, $modelTemplate);
    }

    protected function migration($name)
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

        file_put_contents($this->path . $name . '/Database/migrations/' . $date.'_create_'.get_plural_snake_case_name($name).'_table.php', $template);
    }

    protected function controller($name){
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Controller')
        );

        $file = createFile($this->path . $name . '/Http/Controllers/', getCamelCaseName($name).'Controller');
        return file_put_contents($file, $controllerTemplate);
    }

    protected function request($name){
        $requestTemplate = str_replace(
            [
                '{{modelName}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Request')
        );
        $file = createFile($this->path . $name . '/Http/Requests/', getCamelCaseName($name).'Request');
        return file_put_contents($file, $requestTemplate);
    }

    protected function policy($name){
        $policyTemplate = str_replace(
            [
                '{{modelName}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Policy')
        );

        $file = createFile($this->path . $name . '/Policy/', getCamelCaseName($name).'Policy');
        return file_put_contents($file, $policyTemplate);
    }

    protected function routes($name){
        $routeTemplate = str_replace(
            [
                '{{modelName}}',
                '{{moduleNameCamelCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                getCamelCaseName($name),
                strtolower(Str::plural($name)),
                Str::ucfirst(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Route')
        );

        return file_put_contents($this->path . $name . '/Routes/web.php', $routeTemplate);
    }

    protected function createBlade($name){
         createFile($this->path . $name . '/Views/', 'index.blade');
         createFile($this->path . $name . '/Views/', 'form.blade');
         createFile($this->path . $name . '/Views/', 'create.blade');
         createFile($this->path . $name . '/Views/', 'edit.blade');
    }
}
