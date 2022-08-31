<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;

class UserTenant extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_user_tenants';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    public function user()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User','user_id');
    }  

    public function tenant()
    {
        return $this->belongsTo('App\Models\Tenant','tenant_id');
    }   
}