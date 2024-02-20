<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;


class TimeController extends Controller
{
    public function index()
    {
        $dates = $this->dateSearch();

        if (!session('index')) {
            session()->put('index', 0);
        }
        $users = Work::with('user', 'rest')
            ->workSearch(session('index'), $dates)
            ->select('id', 'user_id', 'work_start', 'work_finish', 'created_at')
            ->selectRaw('TIME_TO_SEC(TIMEDIFF(work_finish, work_start)) as work_time')
            ->Paginate(5);

        foreach ($users as $user) {
            if (empty($user->work_start)) {
                $lastWork = Work::with('rest')
                    ->where('user_id', $user->user_id)
                    ->whereDate('created_at', '<', Carbon::parse($user->created_at))
                    ->orderBy('created_at', 'desc')
                    ->first();

                // 勤務時間（休憩含む）
                $lastWorkStart = Carbon::parse($lastWork->work_start);
                $workFinish = Carbon::parse($user->work_finish);
                $diff = $workFinish->diff($lastWorkStart);
                $workSeconds = $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                //休憩時間
                $lastRests = Rest::where('work_id', $lastWork->id)->get();
                $lastRestSeconds = 0;
                foreach ($lastRests as $lastRest) {
                    $restFinish = Carbon::parse($lastRest->rest_finish);
                    $restStart = Carbon::parse($lastRest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $lastRestSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restSeconds = 0;
                foreach ($user->rest as $rest) {
                    $restFinish = Carbon::parse($rest->rest_finish);
                    $restStart = Carbon::parse($rest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $restSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restSeconds += $lastRestSeconds;

                $workSeconds -= $restSeconds;
                $hours = floor($workSeconds / 3600);
                $minutes = floor(($workSeconds % 3600) / 60);
                $seconds = $workSeconds % 60;
                $totalWork = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                session()->put($user->id, $totalWork);
            }
        }
        return view('attendance', compact('dates', 'users'));
    }

    public function next(Request $request)
    {
        $dates = $this->dateSearch();
        $index = array_search($request->date, $dates) + 1;
        $this->exist($index, $dates);

        return redirect('/attendance');
    }

    public function prev(Request $request)
    {
        $dates = $this->dateSearch();
        $index = array_search($request->date, $dates) - 1;
        $this->exist($index, $dates);

        return redirect('/attendance');
    }

    public function user(Request $request)
    {

        if ($request->id) {
            $id = $request->id;
        } else {
            $id = session('id');
        }

        $private = User::with('work', 'rest')->where('id', $id)->first();
        $date = Carbon::parse(session('date'))->format('Y-m-d');
        if ($date === null) {
            $date = Carbon::now()->format('Y-m-d');
        }
        $firstDay = Carbon::createFromDate(Carbon::parse($date)->format('Y'), Carbon::parse($date)->format('m'), 1);
        $countDays = $firstDay->daysInMonth;

        $days = [];
        for ($day = 0; $day < $countDays; $day++) {
            $firstDay->day($day + 1);
            $days[] = [
                'day' => $day + 1,
                'weekday' => $firstDay->isoFormat('ddd')
            ];
        }

        $works = Work::with('user', 'rest')
            ->where('user_id', $id)
            ->whereDate('created_at', 'LIKE', Carbon::parse($date)->format('Y-m') . '%')
            ->select('id', 'user_id', 'work_start', 'work_finish', 'created_at')
            ->selectRaw('TIME_TO_SEC(TIMEDIFF(work_finish, work_start)) as work_time')
            ->get();
        $workTimeGroup = [];
        $restTimeGroup = [];
        $dateGroup = [];
        $totalRestTime = 0;
        foreach ($works as $work) {
            if (empty($work->work_start)) {
                $lastWork = Work::with('rest')
                    ->where('user_id', $work->user_id)
                    ->whereDate('created_at', '<', Carbon::parse($work->created_at))
                    ->orderBy('created_at', 'desc')
                    ->first();
                // 勤務時間（休憩含む）
                $lastWorkStart = Carbon::parse($lastWork->work_start);
                $workFinish = Carbon::parse($work->work_finish);
                $diff = $workFinish->diff($lastWorkStart);
                $workSeconds = $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                //休憩時間
                $lastRests = Rest::where('work_id', $lastWork->id)->get();

                $lastRestSeconds = 0;
                foreach ($lastRests as $lastRest) {
                    $restFinish = Carbon::parse($lastRest->rest_finish);
                    $restStart = Carbon::parse($lastRest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $lastRestSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restSeconds = 0;
                foreach ($work->rest as $rest) {
                    $restFinish = Carbon::parse($rest->rest_finish);
                    $restStart = Carbon::parse($rest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $restSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restSeconds += $lastRestSeconds;

                $workSeconds -= $restSeconds;
                $hours = floor($workSeconds / 3600);
                $minutes = floor(($workSeconds % 3600) / 60);
                $seconds = $workSeconds % 60;
                $totalWork = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                session()->put($work->id, $totalWork);
            }
            //休憩時間を割り出す
            $restTime = 0;
            foreach ($work->rest as $rest) {
                if ($work->id === $rest->work_id) {
                    $start = Carbon::parse($rest->rest_start);
                    $finish = Carbon::parse($rest->rest_finish);
                    $diff = $finish->diff($start);
                    $restTime += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restTimeGroup[$rest->work_id] = Carbon::parse($restTime)->format('H:i:s');
            }
            $totalRestTime += $restTime;
            //勤務時間を割り出す
            $workTime = $work->work_time - $restTime;
            if (empty($work->work_start)) {
                $workTime = session($work->id);
            } elseif (!empty($work->work_finish)) {
                $workTime = Carbon::parse($workTime)->format('H:i:s');
            } else {
                $workTime = '00:00:00';
            }
            $workTimeGroup[$work->id] = $workTime;
            //実働日数を割り出す
            $dateGroup[] = Carbon::parse($work->created_at)->format('Y-m-d');
        }
        if (!$workTimeGroup) {
            $workTimeGroup = null;
        }
        $workingDays = array_unique($dateGroup);
        if (!$workingDays) {
            $workingDays = [];
        }
        //実働時間を割り出す
        $totalWorkTime = 0;
        if ($workTimeGroup) {
            foreach ($workTimeGroup as $workTime) {
                list($hours, $minutes, $seconds) = explode(':', $workTime);
                $totalWorkTime += $hours * 3600 + $minutes * 60 + $seconds;
            }
            $hours = floor($totalWorkTime / 3600);
            $minutes = floor(($totalWorkTime % 3600) / 60);
            $seconds = $totalWorkTime % 60;
            $totalWorkTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        $totalRestTime = Carbon::parse($totalRestTime)->format('H:i:s');
        return view('attendance_user', compact('private', 'days', 'works', 'date', 'workingDays', 'workTimeGroup', 'totalRestTime', 'restTimeGroup', 'totalWorkTime'));
    }

    public function nextForUser(Request $request)
    {
        $date = $request->date;
        $dateInCarbon = Carbon::parse($request->date);
        $date = Carbon::createFromDate($dateInCarbon->format('Y'), $dateInCarbon->format('m'), 1)->addMonth(1);
        session()->put('id', $request->id);
        return redirect('/attendance/user')->with('date', $date);
    }

    public function prevForUser(Request $request)
    {
        $date = $request->date;
        $dateInCarbon = Carbon::parse($request->date);
        $date = Carbon::createFromDate($dateInCarbon->format('Y'), $dateInCarbon->format('m'), 1)->subMonth(1);
        session()->put('id', $request->id);
        return redirect('/attendance/user')->with('date', $date);
    }

    //使い回し用メソッド
    //indexが存在したら処理を実行するメソッド
    public function exist($index, $dates)
    {
        if (array_key_exists($index, $dates)) {
            session()->put('index', $index);
        }
    }

    //勤務日をまとめるためのメソッド
    public function dateSearch()
    {
        $works = Work::oldest()->get();
        $dates = [];
        foreach ($works as $work) {
            $date = isset($work->work_finish) ? Carbon::parse($work->work_finish)->format('Y-m-d') : Carbon::parse($work->work_start)->format('Y-m-d');
            $dates[] = $date;
        }
        $dates = array_unique($dates);
        sort($dates);

        return $dates;
    }
}
