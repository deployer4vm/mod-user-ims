@extends('layouts.landing')

@section('content')
@include('component.alert')
<div class="card">
    <div class="p-4 p-sm-5">

        <!-- Logo -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <a href="{{url(config('AppConfig.endpoint.home'))}}">
                <img src="{{asset('assets/images/logo.png')}}" alt="" style="max-width:200px;max-height:100px; height:auto">
            </a>
        </div>
        <!-- / Logo -->        
        <h1 class="display-4 text-center">{!! config('AppConfig.system.template.frontend.title') !!}</h1>
        <!-- Form -->
            <h5 class="text-center font-weight-bold mb-4">Reset Password</h5>

            <hr class="mt-0 mb-4">

            <p>
                {{$error_message}}
            </p>
            
            <p class="text-center mt-5 mb-0">
                Kembali ke <a href="{{url(config('AppConfig.endpoint.home'))}}">{{config('AppConfig.client.app_name')}}</a>
            </p>

    </div>
</div>
<div class="text-center p-4">
    {{ config('AppConfig.system.template.admin.footer.text') }}
</div>
@endsection