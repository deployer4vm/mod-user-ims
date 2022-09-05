<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;
use App\Base\Traits\ModelDataTenant;

class UserProfile extends BaseModel
{
    use ModelDataTenant;    
    protected $connection = 'perTenant'; 

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_user_profiles';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    // protected $fillable = [
    //     'user_id',
    //     'avatar',
    //     'gender',
    //     'date_of_birth',
    //     'socnet_facebook',
    //     'socnet_instagram',
    //     'address',
    //     'postal_code'
    //     ];
}