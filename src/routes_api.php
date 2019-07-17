<?php
// use Facades\hpsynapse\moduser\Repositories\RoleRepo;
/**
 * Route API feature Auth
 */
$groupAuth = [
    'prefix' => config('AppConfig.endpoint.api.auth')
];
Route::group($groupAuth,function(){
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
$groupUser = [
    'prefix' => config('AppConfig.endpoint.api.moduser'),
    // 'middleware' => 'auth:api'
];
Route::group($groupUser,function(){
    
    /**
     * Feature User
     */
        //read list resource
        Route::get('/', 'UserController@readList')->name('user.readList'); 
        //read one resource
        Route::get('/{id}', 'UserController@readOne')->name('user.readOne');

        //create resource
        Route::post('/', 'UserController@create')->name('user.create');  
        //update resource
        Route::put('/profile', 'UserController@updateProfile')->name('user.update'); 
        Route::put('/{id}', 'UserController@update')->name('user.update'); 
        Route::put('/{id}/suspend', 'UserController@suspend')->name('user.suspend'); 
        Route::put('/{id}/updatepassword', 'UserController@updatePassword')->name('user.updatePassword'); 
        //delete resource
        Route::delete('/{id}', 'UserController@delete')->name('user.delete');  
    
    /**
     * Module Role
     */
    Route::group(['prefix'=>'role'],function(){ 
        //read list resource
        Route::get('/', 'RoleController@readList')->name('user.role.readList'); 
        //read one resource
        Route::get('/{id}', 'RoleController@readOne')->name('user.role.readOne');

        //create resource
        Route::post('/', 'RoleController@create')->name('user.role.create');  
        //update resource
        Route::put('/{id}', 'RoleController@update')->name('user.role.update'); 
        //delete resource
        Route::delete('/{id}', 'RoleController@delete')->name('user.role.delete');  
    });  
});