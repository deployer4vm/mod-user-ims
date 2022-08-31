<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateAuthLogsTable extends Migration
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
            Schema::create('auth_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('auth_by');
                $table->text('auth_note');
                $table->unsignedInteger('user_id');
                $table->string('rule_group')->default('');
                $table->string('rule_key')->default('');
                $table->string('data')->default('');
                $table->timestamps();
            });
        }

        // create table di database/table per-tenant
        $this->createPerTenant('auth_logs', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->unsignedInteger('auth_by');
            $table->text('auth_note');
            $table->unsignedInteger('user_id');
            $table->string('rule_group')->default('');
            $table->string('rule_key')->default('');
            $table->string('data')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_logs');
    }
}
