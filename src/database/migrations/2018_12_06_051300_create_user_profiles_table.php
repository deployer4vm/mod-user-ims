<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Base\Traits\MigrateDataTenant;

class CreateUserProfilesTable extends Migration
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
            Schema::create('user_profiles', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                
                $table->string('avatar')->default('');

                $table->boolean('gender')->default(0);
                
                $table->date('date_of_birth')->nullable();
                
                $table->string('socnet_facebook')->default('');
                $table->string('socnet_instagram')->default('');
                $table->string('address')->default('');
                            
                $table->string('postal_code')->default('');
                            
                $table->timestamps();
            });
        }
        
        // create table di database/table per-tenant
        $this->createPerTenant('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            
            $table->string('avatar')->default('');

            $table->boolean('gender')->default(0);
            
            $table->date('date_of_birth')->nullable();
            
            $table->string('socnet_facebook')->default('');
            $table->string('socnet_instagram')->default('');
            $table->string('address')->default('');
                        
            $table->string('postal_code')->default('');
                        
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
        Schema::dropIfExists('user_profiles');
    }
}
