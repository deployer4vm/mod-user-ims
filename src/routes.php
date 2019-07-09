<?php

// $routeOpt = route_web_opt(config('bssystem.url.ac'),config('bssystem.url.acapi'));

Route::group([], function(){
    
    //VerificationController
    Route::get('/emailverify', 'Auth\VerificationController@verify')->name('auth.emailVerification');
    Route::get('/emailverify/success', 'Auth\VerificationController@verifySuccess')->name('auth.emailVerification.success');
    Route::get('/emailverify/fail', 'Auth\VerificationController@verifyFail')->name('auth.emailVerification.fail');


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