@extends('layouts.login')

@section('title', 'Daftar')

@section('content')
<div class="col-md-7 col-lg-5 col-sm-12 col-xs-12">

    <form method="POST" action="{{ route('register') }}" class="form login">
        @csrf

        <div class="login__header">
            <h3 class="login__title">Selamat Datang</h3>
        </div>

        <div class="login__body">
            <div class="form__field">
                <input name="name" placeholder="Nama" id="name" type="text" required autocomplete="name" autofocus>
            </div>
            <div class="form__field">
                <input name="email" class="no-top" id="email" type="email" placeholder="Email" required autocomplete="email">
            </div>
            <div class="form__field">
                <input name="password" class="no-top" id="password" type="password" placeholder="Password" required autocomplete="new-password">
            </div>
            <div class="form__field">
                <input class="no-top" id="password-confirm" type="password" placeholder="Ulangi password" name="password_confirmation" required autocomplete="new-password">
            </div>

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

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
                <p>Sudah punya akun? <a class="toggle-link" id="sl" href="{{ route('login') }}">Login</a></p>
            </div>
            <input class="login-btn" type="submit" value="Daftar">
            <div class="clear"></div>
        </div>

    </form>

</div>
@endsection
