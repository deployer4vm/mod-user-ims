<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class AddSystemUserColumnToUsersTable extends Migration
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
            if (!Schema::hasColumn('moduser_users','system_user')) {
                Schema::table('moduser_users', function (Blueprint $table) {
                    $table->boolean('system_user')->default(false)->comment('Apakah user ini adalah user system')->after('level');
                });
            }
        }

        // update data di database/table per-tenant
        $this->tablePerTenant('moduser_users', function (Blueprint $table) {
            $table->boolean('system_user')->default(false)->comment('Apakah user ini adalah user system')->after('level');
        },'system_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moduser_users', function (Blueprint $table) {
            $table->dropColumn('system_user');
        });
    }
}
