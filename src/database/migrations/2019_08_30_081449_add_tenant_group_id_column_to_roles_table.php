<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class AddTenantGroupIdColumnToRolesTable extends Migration
{
    use MigrateDataTenant;
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(config('AppConfig.system.multitenant.active',false) && $this->tenantMigrateMode()==false){
            if (!Schema::hasColumn('roles','tenant_group_id')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->tinyInteger('tenant_group_id')->default(0)->after('id')->comment('0: all tenant, else id main tenant group');
                });
            }
        }
        
        $this->tablePerTenant('roles', function (Blueprint $table) {
            $table->tinyInteger('tenant_group_id')->default(0)->after('id')->comment('0: all tenant, else id main tenant group');
        },'tenant_group_id');
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('roles','tenant_group_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('tenant_group_id');
            });
        }
    }
}
