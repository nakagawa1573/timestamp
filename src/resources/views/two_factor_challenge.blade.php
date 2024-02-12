@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/two_factor_challenge.css') }}">
@endsection

@section('content')
    <section class="content">
        <p class="content__ttl">
            2要素認証設定
        </p>
        <article class="tfa">
            @if (session('status') === 'two-factor-authentication-disabled')
                <div class="tfa__alert">
                    2要素認証は無効になっています
                </div>
            @endif

            @if (session('status') === 'two-factor-authentication-enabled')
                <div class="tfa__alert">
                    ２要素認証は有効になりました
                </div>
            @endif
            <form action="/user/two-factor-authentication" method="post">
                @csrf
                @if (auth()->user()->two_factor_secret)
                    @method('DELETE')
                    <div class="pb-5">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>
                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                        <li>{{ $code }}</li>
                    @endforeach
                    <button class="tfa__disable">
                        無効化
                    </button>
                @else
                    <button class="tfa__enable">
                        有効化
                    </button>
                @endif

            </form>
        </article>
    </section>
@endsection
