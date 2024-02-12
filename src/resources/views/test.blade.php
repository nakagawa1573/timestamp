@extends('layouts.app')

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/auth.css') }}"> --}}
@endsection

@section('content')
    <h2 class="tfa__ttl">
        2段階認証
    </h2>
    <p class="tfa__txt">
        パスコードをメールアドレスに送信しました
    </p>
    <section class="tfa__content">
        @if (session('status') == 'two-factor-authentication-enabled')
            <div class="mb-4 font-medium text-sm">
                以下の、２要素認証の設定を完了して下さい。
            </div>
        @endif

        <form class="tfa__form" action="/user/two-factor-authentication" method="post">
            @csrf
            <input class="tfa__form-input" type="text" placeholder="パスコード">
            <button class="tfa__form-btn" type="submit">
                送信する
            </button>
        </form>
    </section>
@endsection
