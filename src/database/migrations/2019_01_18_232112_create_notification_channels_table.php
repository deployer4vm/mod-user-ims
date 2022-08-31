<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateNotificationChannelsTable extends Migration
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
            Schema::create('notification_channels', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->unsignedTinyInteger('push_type');
                $table->string('push_token')->default('');
                $table->string('channel')->default('');
                $table->timestamps();
            });
        }

        // create table di database/table per-tenant
        $this->createPerTenant('notification_channels', function (Blueprint $table) { 
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('push_type');
            $table->string('push_token')->default('');
            $table->string('channel')->default('');
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
        Schema::dropIfExists('notification_channels');
    }
}
