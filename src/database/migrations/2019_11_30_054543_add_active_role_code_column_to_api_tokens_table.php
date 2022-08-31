<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class AddActiveRoleCodeColumnToApiTokensTable extends Migration
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
            if (!Schema::hasColumn('roles','active_role_code')) {
                Schema::table('api_tokens', function (Blueprint $table) {
                    $table->string('active_role_code')->default('')->comment('role_code aktif yang digunakan')->after('api_token');
                });
            }
        }

        $this->tablePerTenant('api_tokens', function (Blueprint $table) {
            $table->string('active_role_code')->default('')->comment('role_code aktif yang digunakan')->after('api_token');
        },'active_role_code');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            $table->dropColumn('active_role_code');
        });
    }
}
