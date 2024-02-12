@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <section class="content">
        <article class="profile">
            <p class="profile__name">
                {{ $user->name }}
            </p>
            <p class="profile__day">
                登録日：{{ $user->created_at->format('Y-m-d') }}
            </p>
            <p class="profile__email">
                メールアドレス：{{ $user->email }}
            </p>
        </article>
        <article class="btn">
            <form class="btn__works" action="">
                @csrf
                <button>
                    勤務一覧
                </button>
            </form>
            <form class="btn__tfa" action="/mypage/two_factor_setting" method="get">
                @csrf
                <button type="submit">
                    2要素認証設定
                </button>
            </form>
        </article>
    </section>
@endsection
