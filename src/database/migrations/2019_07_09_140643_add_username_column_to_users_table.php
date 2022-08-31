<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class AddUsernameColumnToUsersTable extends Migration
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
            if (!Schema::hasColumn('users','username')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('username')->after('name')->default('');
                });
            }
        }

        // update data di database/table per-tenant
        $this->tablePerTenant('users', function (Blueprint $table) {
            $table->string('username')->after('name')->default('');
        },'name');
    }

    /**
     * Reverse the migrations.  
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
