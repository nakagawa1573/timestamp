@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="ttl">
        会員登録
    </h2>

    <section class="content">
        <form class="content__form" action="/register" method="post">
            @csrf
            <input type="text" class="form__input" name="name" placeholder="名前" value="{{ old('name') }}">
            <div class="form__error">
                &emsp;
                @error('name')
                    {{ $message }}
                @enderror
            </div>
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
            <input type="password" class="form__input" name="password_confirmation" placeholder="確認用パスワード">
            <div class="form__error">
                &emsp;
                @error('password_confirmation')
                    {{ $message }}
                @enderror
            </div>
            <button class="form__btn">
                会員登録
            </button>
        </form>

        <div class="content__box">
            <p class="content__box-txt">
                アカウントをお持ちの方はこちらから
            </p>
            <a class="content__box-link" href="/login">
                ログイン
            </a>
        </div>
    </section>
@endsection
