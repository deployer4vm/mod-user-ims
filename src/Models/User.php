<?php

namespace hpsynapse\moduser\Models;

//use Laravel\Passport\HasApiTokens;//comment jika tidak menggunakan passport
//use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    //comment HasApiTokens jika tidak menggunakan passport
    use NotifiableCustom;//HasApiTokens,

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_idcode','name','username', 'email', 'phone', 'password','auth_password',
        'socialauth_facebook_id','socialauth_facebook_token','socialauth_facebook_data',
        'socialauth_google_id','socialauth_google_token','socialauth_google_data', 'level',
        'note', 'role',  'status', 'banned_note'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'auth_password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function profile()
    {
        return $this->hasOne('hpsynapse\moduser\Models\UserProfile','user_id');
    }    
    public function roles()
    {
        return $this->hasMany('hpsynapse\moduser\Models\UserRole','user_id');
    }    
}
