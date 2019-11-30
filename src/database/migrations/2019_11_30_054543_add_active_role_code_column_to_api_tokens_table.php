<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveRoleCodeColumnToApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            $table->string('active_role_code')->default('')->comment('role_code aktif yang digunakan')->after('api_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            $table->dropColumn('active_role_code');
        });
    }
}
