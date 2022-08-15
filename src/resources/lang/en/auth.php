<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    //----------
    'emailverify_fail_mailnotfound' => 'Verifikasi email gagal. Error : Email tidak ditemukan',
    'emailverify_fail_verificationcodeinvalid' => 'Verifikasi email gagal. Error : Kode verifikasi invalid',
    'phoneverify_fail_phonenotfound' => 'Verifikasi nomor telepon gagal. Error : Nomor telepon tidak ditemukan',
    'phoneverify_fail_verificationcodeinvalid' => 'Verifikasi nomor telepon gagal. Error : Kode verifikasi invalid',
    'resetpassword_fail_mailnotfound' => 'Reset Password Gagal. Error : Email tidak ditemukan',
    'resetpassword_fail_verificationcodeinvalid' => 'Reset Password Gagal. Error : Kode verifikasi invalid',
    //----------
    'logout' => 'Log Out',
    //text lang di halaman forgot password
    'forgotpassword' => [
        'title' => 'Reset Your Password',
        'description' => 'Enter your email address and we will send you a link to reset your password.',
        'emailcaption' => 'Enter your email address',
        'button' => 'Send email address',
        'back_to_login' => 'Back to Login',
        'email' => [
            'subject' => ':website - Forgot password request'
        ],
        'alert' => [
            'forgot_password_failed' => 'Forgot password gagal. Error : :error ',
            'forgot_password_success' => 'Email instruksi forgot password telah dikirim ke email Anda.',
            'email_cannot_be_empty' => 'Email tidak boleh kosong',            
            'email_not_registered' => 'Email tidak terdaftar',
        ]
    ],
    //text lang di halaman loting
    'login' => [
        'title' => 'Login to Your Account',
        'usernamecaption' => 'Username/Email',
        'passwordcaption' => 'Password',
        'forgotpassword' => 'Forgot Password ?',
        'remember_me' => 'Remeber Me',
        'sigincaption' => 'Sign In',
        'dont_have_an_account'=>'Don\'t have an account yet?',
        'signupcaption' => 'Sign Up',
        'alert' => [
            'user_not_found'=> 'User not found',
            'user_banned' => 'Login Failed. Account Banned.',
            'password_fail' => 'Usernaem or password wrong'
        ]
    ],
    //text lang di halaman register
    'register' => [
        'title' => 'Register',
        'namecaption' => 'Name',
        'emailcaption' => 'Email',
        'phonecaption' => 'Phone',
        'usernamecaption' => 'Username',
        'passwordcaption' => 'Password',
        'repasswordcaption' => 'Password Confirmation',
        'remember_me' => 'Remeber Me',
        'sigincaption' => 'Sign In',
        'already_have_an_account'=>'Already have an account?',
        'signupcaption' => 'Sign Up',
        'activation_account_mail' => [
            'subject' => ':website - Aktifasi akun'
        ],
        'verification_mail' => [
            'subject' => ':website - Verifikasi email'
        ],
        'alert' => [                        
            'register_success' => 'Registrasi berhasil. Silahkan cek email Anda untuk melakukan verifikasi email.',
            'register_failed' => 'Registrasi gagal. Error : :error ',
            'password_not_match' => 'Password confirmation not match',
            'tos_confirm_required' => 'Syarat dan ketentuan harus disetujui',
            'validation_error' => 'Validasi error',
        ]
    ],

];
