<?php

namespace hpsynapse\moduser\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ApiToken extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_tokens';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
}