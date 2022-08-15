@extends('layouts.landing')

@section('content')
@include('component.alert')
<div class="card">
    <div class="p-4 p-sm-5">

        <!-- Logo -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <a href="{{route('home')}}">
                <img src="{{asset('assets/images/logo.png')}}" alt="" style="max-width:200px;max-height:100px; height:auto">
            </a>
        </div>
        <!-- / Logo -->
        <h1 class="display-4 text-center">{!! config('AppConfig.system.template.frontend.title') !!}</h1>
        <!-- Form -->
        <form method="POST" action="<?= route('auth.resetPassword', ['verifyCode' => $verifyCode, 'email' => $email]); ?>">
            {{ csrf_field() }}
            @if($setNewPassword)
            <h5 class="text-center font-weight-bold mb-4">Set Password</h5>
            <hr class="mt-0 mb-4">
            <p>
                Silahkan set password untuk bisa login ke akun Anda.
            </p>
            @else
            <h5 class="text-center font-weight-bold mb-4">Reset Password</h5>
            <hr class="mt-0 mb-4">
            <p>
                Untuk melanjutkan proses reset password, silahkan isi password baru yang Anda inginkan.
            </p>
            @endif

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" placeholder="" name="password" maxlength="250">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" placeholder="" name="password_confirmation" maxlength="250">
            </div>

            <button class="btn btn-primary btn-block">Submit</button>
            
        </form>
        <!-- / Form -->

    </div>
</div>
<div class="text-center p-4">
    {{ config('AppConfig.system.template.admin.footer.text') }}
</div>
@endsection