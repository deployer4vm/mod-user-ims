<?php

namespace hpsynapse\moduser\database\seeds;

// use Illuminate\Support\Str;//Str::random(10)
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Base\Traits\SeedDataTenant;

use App\Facades\Tenant;

class UserAuthSeeds extends Seeder
{
    use SeedDataTenant;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->dbTable('moduser_users')->truncate();
        $this->dbTable('moduser_user_profiles')->truncate();
        $this->dbTable('moduser_roles')->truncate();
        $this->dbTable('moduser_user_roles')->truncate();
        // $this->dbTable('moduser_user_tenants')->truncate(); sudah tidak digunakan
        
        $now = Now();
                
        $this->dbTable('moduser_roles')->insert([
            [
                'tenant_id' => $this->tenantId,
                'role_code' => 'webdev',
                'name' => 'Web Developer',
                'level' => 1,
                'rule' => '',
                'system_role' => false,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'tenant_id' => $this->tenantId,
                'role_code' => 'superadmin',
                'name' => 'Super Admin',
                'level' => 2,
                'rule' => '',
                'system_role' => false,
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'tenant_id' => $this->tenantId,
                'role_code' => 'system',
                'name' => 'System',
                'level' => 2,
                'rule' => '',
                'system_role' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        $users = [
            [
                'users' => [
                    'tenant_id' => $this->tenantId,
                    'user_idcode' => '2019010110000001',
                    'name' => 'Web Developer',
                    'username' => 'webdev',
                    'email' => 'webdev@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';webdev;',
                    'level' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 1,
                    'role_id' => 1,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'tenant_id' => $this->tenantId,
                    'user_idcode' => '2019010110000002',
                    'name' => 'Super Admin',
                    'username' => 'superadmin',
                    'email' => 'superadmin@email.com',
                    'password' => Hash::make('secret'),
                    'role' => ';superadmin;',
                    'level' => 2,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_profiles' =>
                [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 2,
                    'role_id' => 2,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ],
            [
                'users' => [
                    'tenant_id' => $this->tenantId,
                    'user_idcode' => '2019010110000000’',
                    'name' => 'API',
                    'username' => Str::random(10),
                    'password' => Hash::make(uniqid()),
                    'role' => ';system;',
                    'system_user' => true,
                    'level' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                'user_profiles' =>
                [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'tenant_id' => $this->tenantId,
                    'user_id' => 3,
                    'role_id' => 3,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
            ]
        ];

        foreach ($users as $key => $value) {
            $this->dbTable('moduser_users')->insert($value['users']);
            $this->dbTable('moduser_user_profiles')->insert($value['user_profiles']);
            $this->dbTable('moduser_user_roles')->insert($value['user_roles']);
        }
    }
}
