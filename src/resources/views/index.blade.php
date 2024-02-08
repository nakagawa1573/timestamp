@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    {{-- {{dd(session('latestWork'))}} --}}
    <p class="ttl">
        {{ $user->name }}さんお疲れ様です!
    </p>
    <p class="error">
        {{ session('message') }}
        @if ($errors->has('work_id'))
            {{ $errors->first('work_id') }}
        @elseif($errors->has('id'))
            {{ $errors->first('id') }}
        @endif
        &nbsp;
    </p>
    <section class="content">
        <div class="content__box">
            <form class="content__form" action="/" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <button class="content__form-btn" type="submit">
                    <span>勤務開始</span>
                </button>
            </form>
            <form class="content__form" action="/" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="id" value="{{ session('work_id') ?? '' }}">
                <button class="content__form-btn" type="submit">
                    勤務終了
                </button>
            </form>
        </div>
        <div class="content__box">
            <form class="content__form" action="/rest" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name='work_id' value="{{ session('work_id') ?? '' }}">
                <input type="hidden" name="rest_start" value="{{ now() }}">
                <button class="content__form-btn">
                    休憩開始
                </button>
            </form>
            <form class="content__form" action="/rest" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name='work_id' value="{{ session('work_id') ?? '' }}">
                <input type="hidden" name="id" value="{{ session('rest_id') ?? '' }}">
                <button class="content__form-btn">
                    <span>休憩終了</span>
                </button>
            </form>
        </div>
    </section>
@endsection
