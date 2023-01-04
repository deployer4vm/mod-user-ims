<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class ChangeAllTablePrefixToModuser extends Migration
{
    use MigrateDataTenant;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('AppConfig.system.multitenant.active', false) && $this->tenantMigrateMode() == false) {
            if (Schema::hasTable('api_tokens')) {
                Schema::rename('api_tokens', 'moduser_api_tokens');
                Schema::rename('auth_logs', 'moduser_auth_logs');
                Schema::rename('notifications', 'moduser_notifications');
                Schema::rename('notification_channels', 'moduser_notification_channels');
                Schema::rename('password_resets', 'moduser_password_resets');
                Schema::rename('roles', 'moduser_roles');
                Schema::rename('system_user_logs', 'moduser_system_user_logs');
                Schema::rename('users', 'moduser_users');
                Schema::rename('user_otp', 'moduser_user_otp');
                Schema::rename('user_profiles', 'moduser_user_profiles');
                Schema::rename('user_roles', 'moduser_user_roles');
                Schema::rename('user_tenants', 'moduser_user_tenants');
            }
        }

        $this->renameTablePerTenant('api_tokens', 'moduser_api_tokens');
        $this->renameTablePerTenant('auth_logs', 'moduser_auth_logs');
        $this->renameTablePerTenant('notifications', 'moduser_notifications');
        $this->renameTablePerTenant('notification_channels', 'moduser_notification_channels');
        $this->renameTablePerTenant('password_resets', 'moduser_password_resets');
        $this->renameTablePerTenant('roles', 'moduser_roles');
        $this->renameTablePerTenant('system_user_logs', 'moduser_system_user_logs');
        $this->renameTablePerTenant('users', 'moduser_users');
        $this->renameTablePerTenant('user_otp', 'moduser_user_otp');
        $this->renameTablePerTenant('user_profiles', 'moduser_user_profiles');
        $this->renameTablePerTenant('user_roles', 'moduser_user_roles');
        $this->renameTablePerTenant('user_tenants', 'moduser_user_tenants');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('moduser_api_tokens', 'api_tokens');
        Schema::rename('moduser_auth_logs', 'auth_logs');
        Schema::rename('moduser_notifications', 'notifications');
        Schema::rename('moduser_notification_channels', 'notification_channels');
        Schema::rename('moduser_password_resets', 'password_resets');
        Schema::rename('moduser_roles', 'roles');
        Schema::rename('moduser_system_user_logs', 'system_user_logs');
        Schema::rename('moduser_users', 'users');
        Schema::rename('moduser_user_otp', 'user_otp');
        Schema::rename('moduser_user_profiles', 'user_profiles');
        Schema::rename('moduser_user_roles', 'user_roles');
        Schema::rename('moduser_user_tenants', 'user_tenants');
    }
}
