<?php

namespace hpsynapse\moduser\Models;

use App\Base\BaseModel;
class NotificationChannel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_channels';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];
    
    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->hasMany('hpsynapse\moduser\Models\User','id','user_id');
    }
}