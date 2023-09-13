<?php

use Illuminate\Support\Facades\Route;

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
        Route::post('/token/validate', 'Auth\TokenApiController@validateToken')->name('auth.api.validatetoken');
        //ubah role user yang sedang loign
        Route::get('/change_role/{role_code}', 'UserController@changeRole')->name('auth.api.changerole');
    });

    /**
     * SSO
     */

});

/**
 * Route API feature User Management
 */
$groupUser = [
    'prefix' => config('AppConfig.endpoint.api.moduser'),
    'middleware' => 'auth:api'
];
Route::get('/notification/setnotif', 'NotificationController@setnotif')->name('user.notification.setnotif');
Route::group($groupUser,function(){

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

    /**
     * Module User System
     */

    Route::group(['prefix'=>'usersystem'],function(){
        Route::get('/', 'UserSystemController@readList')->name('user.usersystem.readList');
        Route::get('/{id}', 'UserSystemController@readOne')->name('user.usersystem.readOne');
        Route::match(['put','post'],'/', 'UserSystemController@create')->name('user.usersystem.create');
        Route::match(['put','post'],'/{id}', 'UserSystemController@update')->name('user.usersystem.update');
        Route::delete('/{id}', 'UserSystemController@delete')->name('user.usersystem.delete');
        Route::match(['put','post'],'/{id}/generate_token', 'UserSystemController@generateToken')->name('user.usersystem.generateToken');
    });

    /**
     * Module Role System
     */

    Route::group(['prefix'=>'rolesystem'],function(){
        Route::get('/', 'RoleSystemController@readList')->name('user.rolesystem.readList');
        Route::get('/{id}', 'RoleSystemController@readOne')->name('user.rolesystem.readOne');
        Route::post('/', 'RoleSystemController@create')->name('user.rolesystem.create');
        Route::put('/{id}', 'RoleSystemController@update')->name('user.rolesystem.update');
        Route::delete('/{id}', 'RoleSystemController@delete')->name('user.rolesystem.delete');
    });

    /**
     * fituf notif
     */
    Route::get('/notification', 'NotificationController@index')->name('user.api.notification');
    Route::get('/notification/type', 'NotificationController@listType')->name('user.api.notification.listType');
    Route::get('/notification/{notificationId}', 'NotificationController@detail')->name('user.api.notification.detail');
    Route::delete('/notification/{notificationId}', 'NotificationController@deleteNotif')->name('user.api.notification.delete');
    Route::post('/notification/read', 'NotificationController@setRead')->name('user.api.notification.setReadBulk');
    Route::post('/notification/{notificationId}/read', 'NotificationController@setRead')->name('user.api.notification.setRead');
    Route::post('/notification/unread', 'NotificationController@setUnread')->name('user.api.notification.setUnreadBulk');
    Route::post('/notification/{notificationId}/unread', 'NotificationController@setUnread')->name('user.api.notification.setUnread');

    /**
     * fitur Broadcast notif
     */
    Route::get('/broadcast', 'BroadcastController@index')->name('user.broadcast');
    Route::post('/broadcast', 'BroadcastController@sendBroadcast')->name('user.broadcast.send');

    /**
     * fitur User
     */

    // Export User
    Route::group(['prefix' => 'export', 'as' => 'download.'], function () {
        Route::get('/', 'UserController@downloadDataUser')->name('dataUser');
        Route::get('/status', 'UserController@downloadDataUserStatus')->name('dataUserStatus');
    });

    //read list resource
    Route::get('/', 'UserController@readList')->name('user.readList');
    //read one resource
    Route::get('/{id}', 'UserController@readOne')->name('user.readOne');

    //create resource
    Route::match(['put','post'],'/', 'UserController@create')->name('user.create');
    //update resource
    Route::match(['put','post'],'/profile', 'UserController@updateProfile')->name('user.update');
    Route::match(['put','post'],'/{id}', 'UserController@update')->name('user.update');
    Route::put('/{id}/ban', 'UserController@ban')->name('user.ban');
    Route::put('/{id}/unban', 'UserController@unban')->name('user.unban');
    Route::put('/{id}/resent-verification-mail', 'UserController@resentVerificationMail')->name('user.resentVerificationMail');
    Route::put('/{id}/updatepassword', 'UserController@updatePassword')->name('user.updatePassword');
    Route::post('/{id}/upload-avatar', 'UserController@uploadAvatar')->name('user.uploadAvatar');
    Route::delete('/{id}/delete-avatar', 'UserController@deleteAvatar')->name('user.deleteAvatar');
    //delete resource
    Route::delete('/{id}', 'UserController@delete')->name('user.delete');

});
