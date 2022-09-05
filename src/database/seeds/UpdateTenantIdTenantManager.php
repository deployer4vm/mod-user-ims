<?php

namespace hpsynapse\moduser\database\seeds;

// use Illuminate\Support\Str;//Str::random(10)
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Base\Traits\SeedDataTenant;

use App\Facades\Tenant;

class UpdateTenantIdTenantManager extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(config('AppConfig.system.multitenant.active',false)){
            DB::table('moduser_users')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_user_profiles')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_password_resets')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_api_tokens')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_roles')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_user_otp')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_user_roles')->update(['tenant_id'=>$this->tenantId]);        
            DB::table('moduser_notifications')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_notification_channels')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_auth_logs')->update(['tenant_id'=>$this->tenantId]);
            DB::table('moduser_system_user_logs')->update(['tenant_id'=>$this->tenantId]);
        }
    }
}
