<?php

use Illuminate\Support\Facades\Route;

/**
 * General web Auth Route
 */
Route::group([
    'prefix' => config('AppConfig.endpoint.web.auth')
], function(){
    //VerificationController (web only, ga ada di api)
    Route::get('/emailverify', 'Auth\VerificationController@verify')->name('auth.emailVerification');
    Route::get('/emailverify/success', 'Auth\VerificationController@verifySuccess')->name('auth.emailVerification.success');
    Route::get('/emailverify/fail', 'Auth\VerificationController@verifyFail')->name('auth.emailVerification.fail');
    
    Route::get('/authentification-failed', function(){
        abort(401);
    })->name('auth.failed');

});

/**
 * Register route admin web jika admin tidak menggunakan full vue
 * -----------------------------------------------------------------------------
 */
if(config('AppConfig.system.use_admin_full_vue',1)!=1){
    /**
     * Controller2 Auth
     */
    Route::prefix(config('AppConfig.endpoint.admin.auth'))->group(function(){
        /**
         * route saat login
         */
        Route::middleware('auth')->group(function(){
            //Auth/LoginController
            Route::get('/logout', 'Auth\LoginController@logout')->name('auth.logout');
            
            //Auth\TokenApiController
            //ubah role user yang sedang loign
            Route::get('/change_role/{role_code}', 'UserController@changeRole')->name('auth.changerole'); 
        });

        //LoginController - appCode untuk SSO
        Route::get('/login/{appCode?}', 'Auth\LoginController@login')->name('auth.login');
        Route::post('/login/{appCode?}', 'Auth\LoginController@doLogin');
        Route::get('/logout/{appCode?}', 'Auth\LoginController@logout')->name('auth.logout');
        // revalidate / cek session untuk SSO
        Route::get('/revalidate/{appCode}', 'Auth\LoginController@revalidate')->name('auth.reValidate');
        
        //RegisterController
        Route::get('/register', 'Auth\RegisterController@register')->name('auth.register');//->middleware('AppsPermissionCheck')
        Route::post('/register', 'Auth\RegisterController@doRegister')->name('auth.doRegister');//->middleware('AppsPermissionCheck')
        
        //ForgotPassowrdController
        Route::get('/forgotpassword', 'Auth\ForgotPasswordController@forgotPassword')->name('auth.forgotPassword');//form forgot password
        Route::post('/forgotpassword', 'Auth\ForgotPasswordController@doForgotPassword'); //send reset email
        
        //ResetPasswordController
        Route::get('/resetpassword', 'Auth\ResetPasswordController@resetPassword')->name('auth.resetPassword'); //form reset password dari link yg didapat di email
        Route::post('/resetpassword', 'Auth\ResetPasswordController@doResetPassword');//prosess reset password
        Route::get('/resetpassword/fail', 'Auth\ResetPasswordController@verifyFail')->name('auth.resetPassword.fail');
        
        //Social Sign On
        Route::get('/socialauth/{provider}', 'Auth\SocialSignOnController@redirectToProvider')->name('socialAuth.login');
                
        //Social Sign On
        Route::get('/socialauth/{provider}/callback', 'Auth\SocialSignOnController@handleProviderCallback')->name('socialAuth.callback');

    });

    /**
     * Controller2 after login
     */
    Route::group([
        'middleware'=> 'auth',
        'prefix' => config('AppConfig.endpoint.admin.moduser')
    ],function(){

        /**
         * User 
         **/       
        //profile resource
        Route::get('/myprofile', 'UserController@profile')->name('user.profile'); 
        Route::put('/myprofile', 'UserController@updateProfile')->name('user.profile');
        
        Route::group(['prefix'=>'data'],function(){ 
            //read list resource
            Route::get('/', 'UserController@readList')->name('user.list'); 
            //form add
            Route::get('/createForm', 'UserController@addNew')->name('user.addNew'); 
            //view edit form
            Route::get('/{id}', 'UserController@readOne')->name('user.edit'); 
            //view resource
            Route::get('/{id}/view', 'UserController@view')->name('user.view');   
            //create resource
            Route::post('/', 'UserController@create')->name('user.create');  
            //update resource 
            Route::put('/{id}', 'UserController@update')->name('user.update'); 
            Route::put('/{id}/ban', 'UserController@ban')->name('user.ban'); 
            Route::put('/{id}/unban', 'UserController@unban')->name('user.unban'); 
            Route::put('/{id}/updatepassword', 'UserController@updatePassword')->name('user.updatePassword'); 
            //delete resource
            Route::delete('/{id}', 'UserController@delete')->name('user.delete');  
        });

        /**
         * Notification - BELUM ADA VERSI BLADE NYA
         */
        // Route::group(['prefix'=>'notification'],function(){ 
        //     Route::get('/', 'NotificationController@index')->name('user.notification');
        //     Route::get('/type', 'NotificationController@listType')->name('user.notification.listType');
        //     Route::get('/{notificationId}', 'NotificationController@detail')->name('user.notification.detail');    
        //     Route::delete('/{notificationId}', 'NotificationController@deleteNotif')->name('user.notification.delete');
        //     Route::post('/read', 'NotificationController@setRead')->name('user.notification.setReadBulk');
        //     Route::post('/{notificationId}/read', 'NotificationController@setRead')->name('user.api.notification.setRead');
        //     Route::post('/unread', 'NotificationController@setUnread')->name('user.notification.setUnreadBulk');
        //     Route::post('/{notificationId}/unread', 'NotificationController@setUnread')->name('user.notification.setUnread');
        // });

        /**
         * Role - BELUM ADA VERSI BLADE NYA
         */        
        // Route::group(['prefix'=>'role'],function(){  
        //     Route::get('/', 'RoleController@index')->name('role.list');
        //     //form add
        //     Route::get('/add', 'RoleController@addNew')->name('role.addNew');
        //     Route::post('/', 'RoleController@create')->name('role.create');
        //     Route::get('/{id}', 'RoleController@edit')->name('role.edit');
        //     Route::put('/{id}', 'RoleController@update')->name('role.update');
        //     Route::delete('/{id}', 'RoleController@delete')->name('role.delete');
        // });

    });

}else{

    Route::prefix(config('AppConfig.endpoint.admin.auth'))->group(function(){
        Route::get('/login/{appCode?}', function(){
            return view('layouts.full_vue.main');
        })->name('auth.login');

        Route::get('/register', function(){
            return view('layouts.full_vue.main');
        })->name('auth.register');//->middleware('AppsPermissionCheck')
    });
}