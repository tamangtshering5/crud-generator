<?php

namespace Pemba\Crud\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CreateController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:controller {name : name of controller} {module : name of module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates controller for module';

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
        $name=$this->argument('name');
        $moduleName=$this->argument('module');
        try {
            $this->info("================ Creating Controller ======================\n");
//                create model
            $this->controller($name,$moduleName);

            $this->info("================ Controller Created Successfully ==========\n");

        }catch (\Exception $e){
            $this->log->error((string) $e);

            $this->error("================ Couldn't Create Controller. ======================");
        }
    }

    protected function getStub($type)
    {
        return file_get_contents(dirname(__FILE__) . "/Stubs/$type.stub");
    }

    protected function controller($name,$moduleName){
        $controllerTemplate = str_replace(
            [
                '{{moduleNameCamelCase}}',
                '{{controllerName}}'
            ],
            [
                getCamelCaseName($moduleName),
                $name
            ],
            $this->getStub('ControllerPlain')
        );

        $file = createFile($this->path . $moduleName . '/Http/Controllers/', $name.'Controller');
        return file_put_contents($file, $controllerTemplate);
    }
}
