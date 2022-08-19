<?php

return [
    'failed' => 'Kredensial tidak ditemukan.',
    'throttle' => 'Terlalu banyak percobaan login. Coba dalam :seconds detik.',
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
        'title' => 'Reset Password',
        'description' => 'Masukan email yang terdaftar, kami akan mengirimkan link reset password ke email tersebut',
        'emailcaption' => 'Masukan email Anda',
        'button' => 'Kirim reset password email',
        'back_to_login' => 'kembali ke halaman login',
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
    //text lang di halaman login
    'login' => [
        'title' => 'Login to Your Account',
        'usernamecaption' => 'Username/Email',
        'passwordcaption' => 'Password',
        'forgotpassword' => 'Lupa Password ?',
        'remember_me' => 'Ingat Aku',
        'sigincaption' => 'Sign In',
        'dont_have_an_account'=>'Belum memiliki Akun?',
        'signupcaption' => 'Sign Up',
        'alert' => [
            'user_not_found'=> 'User tidak ditemukan',
            'user_banned' => 'Login gagal. User diblokir',
            'user_system' => 'Login gagal. User system tidak bisa login',
            'password_fail' => 'Usernaem atau password keliru'
        ]
    ],
    //text lang di halaman register
    'register' => [
        'title' => 'Registrasi',
        'namecaption' => 'Nama',
        'emailcaption' => 'Email',
        'phonecaption' => 'Telepon',
        'usernamecaption' => 'Username',
        'passwordcaption' => 'Password',
        'repasswordcaption' => 'Konfirmasi Password',
        'sigincaption' => 'Sign In',
        'already_have_an_account'=>'Sudah memiliki Akun?',
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
            'password_not_match' => 'Konfirmasi password keliru',
            'tos_confirm_required' => 'Syarat dan ketentuan harus disetujui',
            'validation_error' => 'Validasi error',
        ]
    ],
];