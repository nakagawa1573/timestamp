@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
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
    <div class="date">
        <form action="">
            <button class="date__btn">
                &lt;
            </button>
        </form>
        <p class="date__txt">
            {{-- レコードから参照した年月日を表示 --}}
            2021-11-01
        </p>
        <form action="">
            <button class="date__btn">
                &gt;
            </button>
        </form>
    </div>

    <section class="content">
        <table class="content__table">
            <tr class="table__row">
                <th class="table__header">
                    名前
                </th>
                <th class="table__header">
                    勤務開始
                </th>
                <th class="table__header">
                    勤務終了
                </th>
                <th class="table__header">
                    休憩時間
                </th>
                <th class="table__header">
                    勤務時間
                </th>
            </tr>
            {{-- foreachで複製 --}}
            <tr class="table__row">
                <td class="table__item">
                    テスト太郎
                </td>
                <td class="table__item">
                    10:00:00
                </td>
                <td class="table__item">
                    20:00:00
                </td>
                <td class="table__item">
                    00:30:00
                </td>
                <td class="table__item">
                    09:30:00
                </td>
            </tr>
        </table>
    </section>
    <div class="pages">
        ページネーション
    </div>
@endsection
