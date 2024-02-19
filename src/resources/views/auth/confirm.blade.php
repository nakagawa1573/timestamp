@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <h2 class="confirm__ttl">
        パスワードを入力してください
    </h2>

    <section class="content">
        <form class="content__form" action="/user/confirm-password" method="post">
            @csrf
            <input type="password" class="form__input" name="password" placeholder="パスワード">
            <div class="form__error">
                &emsp;
                @error('password')
                    {{ $message }}
                @enderror
            </div>
            <button class="form__btn">
                送信する
            </button>
        </form>
    </section>
@endsection