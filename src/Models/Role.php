<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;
use App\Base\Traits\ModelDataTenant;

class Role extends BaseModel
{
    use ModelDataTenant;    
    protected $connection = 'perTenant'; 

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_roles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    protected $casts = [
        'rule' => 'array'
    ];
    
    public function tenantGroup()
    {
        return $this->belongsTo('App\Models\TenantGroup','tenant_group_id');
    } 
    public function tenant()
    {
        return $this->belongsTo('App\Models\Tenant','tenant_id');
    }   
}