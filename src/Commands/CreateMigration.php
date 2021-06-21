<?php

namespace Pemba\Crud\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CreateMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:migration {name : name of migration} {tablename : name of table} {module : name of module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates migration for module';

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
        $migrationName=$this->argument('name');
        $moduleName=$this->argument('module');
        $tablename = $this->arguments('tablename');
        try {
            $this->info("================ Creating Migration ======================\n");
//                create model
            $this->migration($migrationName,$moduleName,$tablename);

            $this->info("================ Migration Created Successfully ==========\n");

        }catch (\Exception $e){
            $this->log->error((string) $e);

            $this->error("================ Couldn't Create Migration. ======================");
        }
    }

    protected function getStub($type)
    {
        return file_get_contents(dirname(__FILE__) . "/Stubs/$type.stub");
    }

    protected function migration($migrationName,$module,$tablename)
    {
        $template = str_replace(
            [
                '{{dummyClass}}',
                '{{model_snake_case}}'
            ],
            [
                getCamelCaseName($tablename),
                $tablename
            ],
            $this->getStub('Migration')
        );

        $date = date('Y_m_d_Hms');

        file_put_contents($this->path . $module . '/Database/migrations/' . $date.'_'.$migrationName.'.php', $template);
    }
}
