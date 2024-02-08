@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="date">
        <form action="/attendance/prev" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ $dates[session('index')] ?? '' }}">
            <button class="date__btn" type="submit">
                &lt;
            </button>
        </form>
        <p class="date__txt">
            {{ $dates[session('index')] ?? '' }}
        </p>
        <form action="/attendance/next" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ $dates[session('index')] ?? '' }}">
            <button class="date__btn" type="submit">
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
            @foreach ($users as $user)
                <tr class="table__row">
                    <td class="table__item">
                        {{ $user->user->name }}
                    </td>
                    <td class="table__item">
                        {{ isset($user->work_start) ? Carbon\Carbon::parse($user->work_start)->format('H:i:s') : '' }}
                    </td>
                    <td class="table__item">
                        {{ isset($user->work_finish) ? Carbon\Carbon::parse($user->work_finish)->format('H:i:s') : '' }}
                    </td>
                    <td class="table__item">
                        <?php
                        $restTime = 0;
                        foreach ($user->rest as $rest) {
                            $start = Carbon\Carbon::parse($rest->rest_start);
                            $finish = Carbon\Carbon::parse($rest->rest_finish);
                            $diff = $finish->diff($start);
                            $restTime += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                        }
                        ?>
                        {{ isset($restTime) ? Carbon\Carbon::parse($restTime)->format('H:i:s') : '' }}
                    </td>
                    <td class="table__item">
                        <?php
                        if (isset($restTime)) {
                            $workTime = $user->work_time - $restTime;
                        }
                        ?>
                        @if (empty($user->work_start))
                            {{ session($user->id) }}
                        @elseif (!empty($user->work_finish))
                            {{ isset($workTime) && $workTime >= 0 ? Carbon\Carbon::parse($workTime)->format('H:i:s') : '' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </section>
    <div class="pages">
        {{ $users->appends(request()->query())->links('vendor.pagination.pages') }}
    </div>
@endsection
