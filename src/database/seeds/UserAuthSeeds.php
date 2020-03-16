<?php

namespace hpsynapse\moduser\database\seeds;

// use Illuminate\Support\Str;//Str::random(10)
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAuthSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('user_profiles')->truncate();
        DB::table('roles')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('user_tenants')->truncate();
        
        $now = Now();
                
        DB::table('roles')->insert([
            [
                'role_code' => 'webdev',
                'name' => 'Web Developer',
                'level' => 1,
                'rule' => '',
                'created_at' => $now,
                'updated_at' => $now
            ], [
                'role_code' => 'superadmin',
                'name' => 'Super Admin',
                'level' => 2,
                'rule' => '',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        $users = [
            [
                'users' => [
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
                    'user_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
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
                    'user_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                'user_roles' => [
                    'user_id' => 2,
                    'role_id' => 2,
                    'has_auth_grant' => 1,
                    'is_main_role' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]
        ];

        foreach ($users as $key => $value) {
            DB::table('users')->insert($value['users']);
            DB::table('user_profiles')->insert($value['user_profiles']);
            DB::table('user_roles')->insert($value['user_roles']);
        }
    }
}
