<?php

namespace hpsynapse\moduser\Models;

use Illuminate\Notifications\DatabaseNotification;

use App\Base\Traits\ModelDataTenant;

class Notification extends DatabaseNotification
{
    use ModelDataTenant;    
    protected $connection = 'perTenant'; 

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'moduser_notifications';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at','updated_at'];
    
    protected $casts = [
        'data' => 'array',
        'link_web' => 'array'
    ];
}