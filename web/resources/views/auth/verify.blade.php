@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" style="margin-top: 120px">
            <div class="card">
                <div class="card-header">{{ __('Verifikasi alamat email anda') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Link untuk verifikasi alamat email telah dikirim ke email anda.') }}
                        </div>
                    @endif

                    {{ __('Sebelum melanjutkan, mohon lakukan verifikasi email anda.') }}
                    {{ __('Jika anda tidak menerima email') }}, <a href="{{ route('verification.resend') }}">{{ __('klik disini untuk mengirim ulang') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
