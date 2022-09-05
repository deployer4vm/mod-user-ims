<?php

namespace hpsynapse\moduser\database\seeds;

// use Illuminate\Support\Str;//Str::random(10)
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Base\Traits\SeedDataTenant;

use App\Facades\Tenant;

class UpdateTenantIdPerTenant extends Seeder
{
    use SeedDataTenant;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->dbTable('moduser_users')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_user_profiles')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_password_resets')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_api_tokens')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_roles')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_user_roles')->update(['tenant_id'=>$this->tenantId]);        
        $this->dbTable('moduser_notifications')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_notification_channels')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_auth_logs')->update(['tenant_id'=>$this->tenantId]);
        $this->dbTable('moduser_system_user_logs')->update(['tenant_id'=>$this->tenantId]);
    }
}
