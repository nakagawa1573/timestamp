@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="ttl">
        会員登録
    </h2>

    <section class="content">
        <form class="content__form" action="">
            <input type="text" class="form__input" placeholder="名前">
            <input type="text" class="form__input" placeholder="メールアドレス">
            <input type="text" class="form__input" placeholder="パスワード">
            <input type="text" class="form__input" placeholder="確認用パスワード">
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
