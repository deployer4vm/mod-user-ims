<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;
use App\Base\Traits\ModelDataTenant;

class PasswordReset extends BaseModel
{
    use ModelDataTenant;    
    protected $connection = 'perTenant'; 
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_password_resets';
    protected  $primaryKey  = 'email';
    const UPDATED_AT = null;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at'];
}