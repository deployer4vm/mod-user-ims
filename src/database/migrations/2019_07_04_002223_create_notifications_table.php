<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateNotificationsTable extends Migration
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
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->string('link_web')->default('');
                $table->string('link_apps')->default('');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // create table di database/table per-tenant
        $this->createPerTenant('notifications', function (Blueprint $table) {            
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->string('link_web')->default('');
            $table->string('link_apps')->default('');
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
