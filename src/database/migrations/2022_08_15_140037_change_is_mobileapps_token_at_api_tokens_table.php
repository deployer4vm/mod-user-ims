<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class ChangeIsMobileappsTokenAtApiTokensTable extends Migration
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
            // update data di database utama (tenant manager)
            if (Schema::hasColumn('moduser_api_tokens','is_mobileapps_token')) {
                Schema::table('moduser_api_tokens', function (Blueprint $table) {
                    $table->renameColumn('is_mobileapps_token', 'is_permanent');
                });
            }
        }

        // update data di database/table per-tenant
        $this->tablePerTenant('moduser_api_tokens', function (Blueprint $table) {            
            $table->renameColumn('is_mobileapps_token', 'is_permanent');
        },'is_mobileapps_token');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moduser_api_tokens', function (Blueprint $table) {
            $table->renameColumn('is_permanent', 'is_mobileapps_token');
        });
    }
}
