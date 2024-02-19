@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="tfa__ttl">
        2要素認証
    </h2>
    <p class="tfa__txt">
        コードか、リカバリーコードを入力してください
    </p>
    <section class="tfa__content">
        <form class="tfa__form" action="/two-factor-challenge" method="post">
            @csrf
            <input class="tfa__form--input" type="text" placeholder="コード" name="code">
            <button class="tfa__form--btn" id="btn_1" type="submit">
                送信する
            </button>
        </form>
        <form class="tfa__form" action="/two-factor-challenge" method="post">
            @csrf
            <input class="tfa__form--input" type="text" placeholder="リカバリーコード" name="recovery_code">
            <button class="tfa__form--btn" type="submit">
                送信する
            </button>
        </form>
    </section>
@endsection
