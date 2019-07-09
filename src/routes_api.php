<?php

/**
 * Route API feature Auth
 */
Route::group(['prefix'=>config('AppConfig.endpoint.api.auth')],function(){
    //Auth/LoginController
    Route::post('/login', 'Auth\LoginController@apiLogin')->name('auth.api.login');    

    //Auth/RegisterController
    Route::post('/register', 'Auth\RegisterController@apiRegister')->name('auth.api.register');

    //Auth/ForgotPasswordController
    Route::post('/forgotpassword', 'Auth\ForgotPasswordController@doForgotPassword')->name('auth.api.register');

    //Auth/TokenApiController - generate token akses tanpa user
    // Route::post('/token', 'Auth\TokenApiController@generateToken')->name('auth.api.generatetoken');

    Route::middleware('auth:api')->group(function(){
        //Auth/LoginController
        Route::get('/logout', 'Auth\LoginController@apiLogout')->name('auth.api.logout');
        //TokenApiController
        // Route::post('/token/validate', 'Auth\TokenApiController@validateToken')->name('auth.api.validatetoken');
    });
});

/**
 * Route API feature User Management
 */
Route::group(['prefix'=>config('AppConfig.endpoint.api.moduser'),'middleware'=>['auth:api'] ],function(){
    
    /**
     * Feature User
     */
    Route::get('/testing', function(){
        $wekdut = new wekdut();
        $query = Role::where('role','taeun');
        $query = $wekdut->_setFilterWhere($query,[
            ['wekdut','!=','taeun'],
            ['or orwekdut','taeun'],
            [
                'or',
                ['innested','yeah']
            ]
        ]);
        dd($query->toSql());
        // var_dump(Role::where('role_code','taeun')->first());
    });
    
    /**
     * Module Role
     */
});