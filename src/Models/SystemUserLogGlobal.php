<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;

class SystemUserLogGlobal extends BaseModel
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_system_user_logs';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}