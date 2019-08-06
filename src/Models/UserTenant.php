<?php

namespace hpsynapse\moduser\Models;

use Illuminate\Database\Eloquent\Model;

class UserTenant extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_tenants';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];
    
    public function user()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User','role_id');
    }   
}