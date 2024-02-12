@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="ttl">
        ログイン
    </h2>

    <section class="content">
        <form class="content__form" action="/login" method="post">
            @csrf
            <input type="text" class="form__input" name="email" placeholder="メールアドレス" value="{{ old('email') }}">
            <div class="form__error">
                &emsp;
                @error('email')
                    {{ $message }}
                @enderror
            </div>
            <input type="password" class="form__input" name="password" placeholder="パスワード">
            <div class="form__error">
                &emsp;
                @error('password')
                    {{ $message }}
                @enderror
            </div>
            <button class="form__btn">
                ログイン
            </button>
        </form>

        <div class="content__box">
            <p class="content__box--txt">
                アカウントをお持ちでない方はこちらから
            </p>
            <a class="content__box--link" href="/register">
                会員登録
            </a>
        </div>
    </section>
@endsection
