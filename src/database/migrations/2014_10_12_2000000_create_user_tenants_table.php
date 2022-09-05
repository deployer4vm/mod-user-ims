<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateUserTenantsTable extends Migration
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
            Schema::create('user_tenants', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager');
                $table->unsignedBigInteger('user_id')->default(0);
                
                $table->timestamps();

            });
        }
        
        // create table di database/table per-tenant
        $this->createPerTenant('user_tenants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager');
            $table->unsignedBigInteger('user_id')->default(0);
            
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
        Schema::dropIfExists('user_tenants');
    }
}
