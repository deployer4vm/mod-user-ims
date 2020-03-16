<?php

namespace hpsynapse\moduser\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UpdateRoleFromJson extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moduser:aclupdate '.
        '{--connection= : [OPTIONAL] default akan menggunakan koneksi default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data json role di /app/MainApp/config/acl/* ke table roles';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $connection = $this->option('connection'); 
        $connection = !$connection?config('database.default'):$connection;

        $filenames = glob(app_path('MainApp/config/acl/*'));
                   
        $paths = array_map(function ($filename) {
            return str_ireplace('.json','',str_replace(app_path('MainApp/config/acl/'),'',$filename));
        }, $filenames);
        
        foreach ($paths as $roleCode) {
            DB::connection($connection)->table('roles')
                ->where('role_code',$roleCode)
                ->update(['rule' => file_get_contents(app_path('MainApp/config/acl/'.$roleCode.'.json'))]);
            $this->info('Update : '.$roleCode);
        }

        $this->info('SUCCESS!');
    }
}