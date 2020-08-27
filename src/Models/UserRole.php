<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;

class UserRole extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_roles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    public function role()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\Role','role_id');
    }   
}