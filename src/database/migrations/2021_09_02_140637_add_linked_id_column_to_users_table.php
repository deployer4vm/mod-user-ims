<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Base\Traits\MigrateDataTenant;

class AddLinkedIdColumnToUsersTable extends Migration
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
            // update data di database utama (tenant manager)
            if (!Schema::hasColumn('roles','linked_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('linked_id')->default(0)->comment('Id table lain yg berelasi dengan user, 0 jika tidak berelasi')->after('status');
                });
            }
        }
        
        // update data di database/table per-tenant
        $this->tablePerTenant('users', function (Blueprint $table) {
            $table->unsignedBigInteger('linked_id')->default(0)->comment('Id table lain yg berelasi dengan user, 0 jika tidak berelasi')->after('status');
        },'linked_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('linked_id');
        });
    }
}
