<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class MoveAvatarColumnToUsersTable extends Migration
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
            if (!Schema::hasColumn('users','avatar')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('avatar')->after('user_idcode')->default('');
                });
            }
            
            if (Schema::hasColumn('user_profiles','avatar')) {
                Schema::table('user_profiles', function (Blueprint $table) {
                    $table->dropColumn('avatar');
                });     
            }   
        }

        $this->tablePerTenant('users', function (Blueprint $table) {
            $table->string('avatar')->after('user_idcode')->default('');
        },'avatar');

        $this->tablePerTenant('user_profiles', function (Blueprint $table) {
            $table->dropColumn('avatar');
        },'avatar',true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('avatar')->after('user_id')->default('');
        });
    }
}
