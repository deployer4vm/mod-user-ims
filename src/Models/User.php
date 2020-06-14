<?php

namespace hpsynapse\moduser\Models;

//use Laravel\Passport\HasApiTokens;//comment jika tidak menggunakan passport
use Illuminate\Notifications\Notifiable;
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
        'all_tenant','user_idcode','name','username', 'email', 'phone', 'password','auth_password',
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

    
    public function mainRole()
    {
        return $this->hasOne('hpsynapse\moduser\Models\UserRole','user_id')->has('role')->where('is_main_role',1);
    }

    // public function mainRole()
    // {
    //     // dd($this->roles()->where('is_main_role',1)->first()->role());
    //     // dd($this->roles()->where('is_main_role',1));
    //     return $this->roleutama()->role();//\hpsynapse\moduser\Models\UserRole::where('is_main_role',1)->where('user_id',$this->user_id);
    // }

    public function userTenant()
    {
        return $this->hasMany('hpsynapse\moduser\Models\UserTenant','user_id');
    }

    public function tenant()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant', // table tujuan
            'hpsynapse\moduser\Models\UserTenant', // table transaksi
            'tenant_id', // Foreign key on table transaksi...
            '', // Foreign key on table tujuan...
            '', // Local key on main model table...
            'user_id' // Local key on table transaksi...
        );
    }    
}
