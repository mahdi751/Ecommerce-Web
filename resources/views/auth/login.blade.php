@extends('layouts.app')

@section('content')

<head>
<meta name="google-signin-client_id" content="1003600153635-c2qdgdrd5dgqthh4m03rec3o1s8u0utb.apps.googleusercontent.com">
<script src="https://accounts.google.com/gsi/client" async></script>

<script>
function loginCallBack(){
    
}
    </script>
</head>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div id="g_id_onload"
                            data-client_id="1003600153635-c2qdgdrd5dgqthh4m03rec3o1s8u0utb.apps.googleusercontent.com"
                            data-context="signin"
                            data-ux_mode="popup"
                            data-callback="loginCallBack"
                            data-auto_prompt="false">
                        </div>

                        <div class="g_id_signin"
                            data-type="standard"
                            data-shape="pill"
                            data-theme="filled_black"
                            data-text="continue_with"
                            data-size="large"
                            data-logo_alignment="left">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
