<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class AddSystemRoleColumnToRolesTable extends Migration
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
            if (!Schema::hasColumn('moduser_roles','system_role')) {
                Schema::table('moduser_roles', function (Blueprint $table) {
                    $table->boolean('system_role')->default(false)->comment('Apakah role ini adalah role system')->after('level');
                });
            }
        }

        // update data di database/table per-tenant
        $this->tablePerTenant('moduser_roles', function (Blueprint $table) {
            $table->boolean('system_role')->default(false)->comment('Apakah role ini adalah role system')->after('level');
        },'system_role');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moduser_roles', function (Blueprint $table) {
            $table->dropColumn('system_role');
        });
    }
}
