<?php

namespace hpsynapse\moduser\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Base\Traits\ModelDataTenant;

class ApiToken extends Authenticatable
{
    use ModelDataTenant;    
    protected $connection = 'perTenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_api_tokens';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}