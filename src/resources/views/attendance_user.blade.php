@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_user.css') }}">
@endsection

<?php
$date = Carbon\Carbon::parse($date)->format('Y-m');
?>

@section('content')
    <p class="ttl">
        個人勤怠表（{{ $private->name }}）
    </p>
    <section class="date">
        <form action="/attendance/user-prev" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name='id' value="{{ $private->id }}">
            <button class="date__btn" type="submit">
                &lt;
            </button>
        </form>
        <p class="date__txt">
            {{ $date }}
        </p>
        <form action="/attendance/user-next" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name='id' value="{{ $private->id }}">
            <button class="date__btn" type="submit">
                &gt;
            </button>
        </form>
    </section>
    <section class="total">
        <table class="total__table">
            <tr class="total__row">
                <th>
                    実働日数
                </th>
                <th>
                    実働時間
                </th>
                <th>
                    総休憩時間
                </th>
            </tr>
            <tr class="total__row">
                <td>
                    {{ count($workingDays) }}日
                </td>
                <td>
                    {{ $totalWorkTime == 0 ? '00:00:00' : $totalWorkTime }}
                </td>
                <td>
                    {{ $totalRestTime }}
                </td>
            </tr>
        </table>
    </section>

    <section class="content">
        <table class="content__header">
            <tr class="content__header--row">
                <th class="content__header--date">
                    日付
                </th>
                <th>
                    勤務開始
                </th>
                <th>
                    勤務終了
                </th>
                <th>
                    休憩時間
                </th>
                <th>
                    勤務時間
                </th>
            </tr>
        </table>
        @foreach ($days as $day)
            <table class="content__table">
                <?php
                $now = sprintf('%02d', $day['day']);
                $count = 2;
                foreach ($works as $work) {
                    if ($now == Carbon\Carbon::parse($work->created_at)->format('d') && $date == Carbon\Carbon::parse($work->created_at)->format('Y-m')) {
                        $count++;
                    }
                }
                ?>
                <tr class="content__date">
                    <td rowspan="{{ $count }}">
                        {{ $now . '(' . $day['weekday'] . ')' }}
                    </td>
                </tr>
                <?php
                $flag = false;
                $workTimeTotal = [];
                ?>
                @foreach ($works as $work)
                    @if (
                        $now == Carbon\Carbon::parse($work->created_at)->format('d') &&
                            $date == Carbon\Carbon::parse($work->created_at)->format('Y-m'))
                        <tr class="content__row">
                            <td>
                                {{ isset($work->work_start) ? Carbon\Carbon::parse($work->work_start)->format('H:i:s') : '' }}
                            </td>
                            <td>
                                {{ isset($work->work_finish) ? Carbon\Carbon::parse($work->work_finish)->format('H:i:s') : ''}}
                            </td>
                            <td>
                                @if (array_key_exists($work->id, $restTimeGroup))
                                    {{$restTimeGroup[$work->id]}}
                                @else
                                00:00:00
                                @endif
                            </td>
                            <td>
                                {{ isset($workTimeGroup[$work->id]) && $workTimeGroup[$work->id] > 0 ? $workTimeGroup[$work->id] : ''}}
                            </td>
                        </tr>
                        <?php $flag = true; ?>
                    @endif
                @endforeach
                @if (!$flag)
                    <tr class="test__row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
            </table>
        @endforeach
    </section>
@endsection
