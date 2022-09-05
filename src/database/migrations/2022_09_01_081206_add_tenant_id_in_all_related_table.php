<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class AddTenantIdInAllRelatedTable extends Migration
{    
    use MigrateDataTenant;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        // update data di database utama (tenant manager)
        if(config('AppConfig.system.multitenant.active',false) && $this->tenantMigrateMode()==false){
            if (!Schema::hasColumn('moduser_users','tenant_id')) {
                Schema::table('moduser_users', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });           
            }            
            if (!Schema::hasColumn('moduser_user_profiles','tenant_id')) {     
                Schema::table('moduser_user_profiles', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_password_resets','tenant_id')) {    
                Schema::table('moduser_password_resets', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->first();
                });
            }            
            if (!Schema::hasColumn('moduser_user_otp','tenant_id')) {    
                Schema::table('moduser_user_otp', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->first();
                });
            }          
            if (!Schema::hasColumn('moduser_api_tokens','tenant_id')) {    
                Schema::table('moduser_api_tokens', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_user_roles','tenant_id')) {    
                Schema::table('moduser_user_roles', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_roles','tenant_id')) {    
                Schema::table('moduser_roles', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_notifications','tenant_id')) {    
                Schema::table('moduser_notifications', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_notification_channels','tenant_id')) {    
                Schema::table('moduser_notification_channels', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_auth_logs','tenant_id')) {    
                Schema::table('moduser_auth_logs', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }            
            if (!Schema::hasColumn('moduser_system_user_logs','tenant_id')) {    
                Schema::table('moduser_system_user_logs', function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
                });
            }
        }
        
        // update data di database/table per-tenant
        $this->tablePerTenant('moduser_users', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_password_resets', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->first();
        },'tenant_id');
        $this->tablePerTenant('moduser_user_otp', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->first();
        },'tenant_id');
        $this->tablePerTenant('moduser_api_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_notification_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_auth_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
        $this->tablePerTenant('moduser_system_user_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->default(0)->comment('tenant id, 0 : berarti sistem tidak multi tenant, atau data di tenant manager')->after('id');
        },'tenant_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
