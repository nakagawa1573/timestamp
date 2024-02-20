@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endsection

@section('content')
    <p class="ttl">
        ユーザー一覧
    </p>
    <section class="group__search">
        <form class="search__form" action="/users" method="get" novalidate>
            @csrf
            <div class="search__type--box">
                <select class="search__type" name="status" required>
                    <option value="" disabled selected style="display:none;">
                        勤務状態
                    </option>
                    <option value="全て" {{ isset($status) && $status === '全て' ? 'selected' : '' }}>
                        全て
                    </option>
                    <option value="勤務中" {{ isset($status) && $status === '勤務中' ? 'selected' : '' }}>
                        勤務中
                    </option>
                    <option value="勤務外" {{ isset($status) && $status === '勤務外' ? 'selected' : '' }}>
                        勤務外
                    </option>
                    <option value="休憩中" {{ isset($status) && $status === '休憩中' ? 'selected' : '' }}>
                        休憩中
                    </option>
                </select>
            </div>
            <input class="search__name" type="text" name="keyword" placeholder="名前を入力してください"
                value="{{ $keyword ?? '' }}">
            <button class="search__btn" type="submit">
                検索
            </button>
        </form>
    </section>

    <section class="content">
        <table class="table__users">
            @foreach ($users as $user)
                <tr class="group__user">
                    <td class="user__type">
                        @if ($user->status === '勤務外')
                            <div class="user__type--0">
                                勤務外
                            </div>
                        @elseif ($user->status === '勤務中')
                            <div class="user__type--1">
                                勤務中
                            </div>
                        @else
                            <div class="user__type--2">
                                休憩中
                            </div>
                        @endif
                    </td>
                    <td class="user__name">
                        {{ $user->name }}
                    </td>
                    <td class="user__date">
                        最終勤務日&ensp;:&ensp;{{ session('last_work')[$user->id] }}
                    </td>
                    <td>
                        <form class="user__btn" action="/attendance/user" method="get">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <button type="submit">
                                勤怠表
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </section>
    <div class="pages">
        {{ $users->appends(request()->query())->links('vendor.pagination.pages') }}
    </div>
@endsection
