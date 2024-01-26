@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('menu')
    <nav class="header__nav">
        <ul class="header__nav-list">
            <li class="header__nav-list__item">
                <a class="header__nav-list__item-link" href="">
                    ホーム
                </a>
            </li>
            <li class="header__nav-list__item">
                <a class="header__nav-list__item-link" href="">
                    日付一覧
                </a>
            </li>
            <li class="header__nav-list__item">
                <form action="">
                    <button  class="header__nav-list__item-btn">
                        ログアウト
                    </button>
                </form>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <p class="ttl">
        {{-- ログインユーザーの名前を表示 --}}
        福場凛太郎さんお疲れ様です!
    </p>
    <section class="content">
        <form class="content__box">
            <button class="content__form-btn">
                <span>勤務開始</span>
            </button>
            <button class="content__form-btn">
                勤務終了
            </button>
        </form>
        <form class="content__box">
            <button class="content__form-btn">
                休憩開始
            </button>
            <button class="content__form-btn">
                <span>休憩終了</span>
            </button>
        </form>
    </section>
@endsection
