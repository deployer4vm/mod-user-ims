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
        $now = Now();
        DB::table('users')->insert([
            'user_idcode' => '2019010110000001',
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('secret'),
            'role' => ';admin;',
            'level' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('roles')->insert([
            'role_code'=>'admin',
            'name'=>'Admin',
            'rule'=> '',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('user_roles')->insert([
            'user_id'=>1,
            'role_id'=>1,
            'has_auth_grant'=> 1,
            'is_main_role'=>1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
