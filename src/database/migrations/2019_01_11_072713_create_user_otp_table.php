<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateUserOtpTable extends Migration
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
            Schema::create('user_otp', function (Blueprint $table) {
                $table->unsignedInteger('user_id');
                $table->string('phone');
                $table->string('token');
                $table->dateTime('timeout')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        // create table di database/table per-tenant
        $this->createPerTenant('user_otp', function (Blueprint $table) { 
            $table->unsignedInteger('user_id');
            $table->string('phone');
            $table->string('token');
            $table->dateTime('timeout')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_otp');
    }
}
