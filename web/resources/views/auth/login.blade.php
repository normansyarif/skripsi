@extends('layouts.login')

@section('title', 'Login')

@section('content')
<div class="col-md-7 col-lg-5 col-sm-12 col-xs-12">

    <form method="POST" action="{{ route('login') }}" class="form login">
        @csrf

        <div class="login__header">
            <h3 class="login__title">Selamat Datang</h3>
        </div>

        <div class="login__body">
            <div class="form__field">
                <input id="email" name="email" type="email" placeholder="Email" required autocomplete="email" autofocus value="{{ old('email') }}">
            </div>
            <div class="form__field">
                <input class="no-top" id="password" type="password" placeholder="Password" name="password" required autocomplete="current-password">
            </div>

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

        </div>

        <div class="login__footer">
            <div class="forgot">
                <p>Belum punya akun? <a class="toggle-link" id="sl" href="{{ route('register') }}">Daftar</a></p>
                <p>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                    @endif
                </p>
            </div>
            <input class="login-btn" type="submit" value="Login">
            <div class="clear"></div>
        </div>

    </form>

</div>
@endsection
