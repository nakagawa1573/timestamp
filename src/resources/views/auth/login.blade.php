@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="ttl">
        ログイン
    </h2>

    <section class="content">
        <form class="content__form" action="">
            <input type="text" class="form__input" placeholder="メールアドレス">
            <input type="text" class="form__input" placeholder="パスワード">
            <button class="form__btn">
                ログイン
            </button>
        </form>

        <div class="content__box">
            <p class="content__box-txt">
                アカウントをお持ちでない方はこちらから
            </p>
            <a class="content__box-link" href="/register">
                会員登録
            </a>
        </div>
    </section>
@endsection
