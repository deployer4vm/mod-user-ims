<?php

use Illuminate\Support\Facades\Route;

//Auth Route
Route::group([
    'prefix' => config('AppConfig.endpoint.web.auth')
], function(){
    //VerificationController
    Route::get('/emailverify', 'Auth\VerificationController@verify')->name('auth.emailVerification');
    Route::get('/emailverify/success', 'Auth\VerificationController@verifySuccess')->name('auth.emailVerification.success');
    Route::get('/emailverify/fail', 'Auth\VerificationController@verifyFail')->name('auth.emailVerification.fail');
    
    Route::get('/authentification-failed', function(){
        abort(401);
    })->name('auth.login');

    Route::middleware('auth')->group(function(){
        //Auth/LoginController
        Route::get('/logout', 'Auth\LoginController@apiLogout')->name('auth.logout');
        //TokenApiController
        //ubah role user yang sedang loign
        Route::get('/change_role/{role_code}', 'Auth\TokenApiController@changeRole')->name('auth.changerole'); 
    });


//         Route::get('/testing', function($apps_code) {
//             dd(UserRepo::generateUserIdcode());
//             dd(AppsClient::first()->toArray());
//         })->name('auth.testing');

//         //LoginController 
//         Route::get('/login', 'Auth\LoginController@login')->name('auth.login');
//         Route::post('/login', 'Auth\LoginController@doLogin');
//         Route::get('/logout', 'Auth\LoginController@logout')->name('auth.logout');

//         //RegisterController
//         Route::get('/register', 'Auth\RegisterController@register')->middleware('AppsPermissionCheck')->name('auth.register');
//         Route::post('/register', 'Auth\RegisterController@doRegister')->middleware('AppsPermissionCheck')->name('auth.doRegister');

//         //ForgotPassowrdController
//         Route::get('/forgotpassword', 'Auth\ForgotPasswordController@forgotPassword')->name('auth.forgotPassword');//form forgot password
//         Route::post('/forgotpassword', 'Auth\ForgotPasswordController@doForgotPassword'); //send reset email

//         //ResetPasswordController
//         Route::get('/resetpassword', 'Auth\ResetPasswordController@resetPassword')->name('auth.resetPassword'); //form reset password dari link yg didapat di email
//         Route::post('/resetpassword', 'Auth\ResetPasswordController@doResetPassword');//prosess reset password
//         Route::get('/resetpassword/fail', 'Auth\ResetPasswordController@verifyFail')->name('auth.resetPassword.fail');

//         //Social Sign On
//         Route::get('/socialauth/{provider}', 'Auth\SocialSignOnController@redirectToProvider')->name('socialAuth.login');


//     //Social Sign On
//     Route::get('/socialauth/{provider}/callback', 'Auth\SocialSignOnController@handleProviderCallback')->name('socialAuth.callback');
});

/**
 * Module notif
 */
Route::get('/notification', 'NotificationController@index')->name('user.notification');
Route::get('/notification/type', 'NotificationController@listType')->name('user.notification.listType');
Route::get('/notification/{notificationId}', 'NotificationController@detail')->name('user.notification.detail');    
Route::delete('/notification/{notificationId}', 'NotificationController@deleteNotif')->name('user.notification.delete');
Route::post('/notification/read', 'NotificationController@setRead')->name('user.notification.setReadBulk');
Route::post('/notification/{notificationId}/read', 'NotificationController@setRead')->name('user.api.notification.setRead');
Route::post('/notification/unread', 'NotificationController@setUnread')->name('user.notification.setUnreadBulk');
Route::post('/notification/{notificationId}/unread', 'NotificationController@setUnread')->name('user.notification.setUnread');

