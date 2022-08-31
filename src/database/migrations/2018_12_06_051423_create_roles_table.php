<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateRolesTable extends Migration
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
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('tenant_id')->default(0);
                $table->string('role_code');
                $table->tinyInteger('level')->default(2);
                $table->string('name');
                $table->text('rule')->nullable();
                $table->timestamps();
            });
        }
        
        // create table di database/table per-tenant
        $this->createPerTenant('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tenant_id')->default(0);
            $table->string('role_code');
            $table->tinyInteger('level')->default(2);
            $table->string('name');
            $table->text('rule')->nullable();
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
        Schema::dropIfExists('roles');
    }
}
