<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class CreateUsersCategoriesTable extends Migration
{
    use MigrateDataTenant;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // eksekusi hanya saat migrasi dilakukan via artisan migrate (migrasi pertama)
        if(!$this->tenantMigrateMode()){
            // create table di database utama untuk tenant manager
            Schema::create('moduser_user_categories', function (Blueprint $table) {
                $table->bigIncrements('id');            
                $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager');
                $table->unsignedBigInteger('created_by')->default(0)->comment('user id yang create data ini');
                $table->unsignedBigInteger('updated_by')->default(0)->comment('last user id yg update');

                $table->text('name')->nullable();
                $table->text('description')->nullable();

                $table->timestamps();
            });
        }
        
        // jika multi tenant tipe beda table atau beda datase aktif
        if(config('AppConfig.system.multitenant.active',false) && config('AppConfig.system.multitenant.data_mode',1)!=1){
            // create table di masing-masing tenang
            $this->createPerTenant('moduser_user_categories', function (Blueprint $table) {
                $table->bigIncrements('id');            
                $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager');
                $table->unsignedBigInteger('created_by')->default(0)->comment('user id yang create data ini');
                $table->unsignedBigInteger('updated_by')->default(0)->comment('last user id yg update');

                $table->text('name')->nullable();
                $table->text('description')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moduser_user_categories');
    }
}
