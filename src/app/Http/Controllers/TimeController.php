<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;


class TimeController extends Controller
{
    public function atte()
    {
        $dates = Work::dateGroup()->pluck('date')->toArray();

        if (!session('index')) {
            session()->put('index', 0);
        }
        $users = Work::with('user', 'rest')
            ->workSearch(session('index'), $dates)
            ->select('id', 'user_id', 'work_start', 'work_finish','created_at')
            ->selectRaw('TIME_TO_SEC(TIMEDIFF(work_finish, work_start)) as work_time')
            ->Paginate(5);


        foreach ($users as $user) {
            if (empty($user->work_start)) {
                $lastWork = Work::with('rest')
                    ->where('user_id', $user->user_id)
                    ->whereDate('created_at', '<=', Carbon::parse($user->created_at))
                    ->orderBy('created_at', 'desc')
                    ->skip(1)
                    ->first();

                // 勤務時間（休憩含む）
                $lastWorkStart = Carbon::parse($lastWork->work_start);
                $workFinish = Carbon::parse($user->work_finish);
                $diff = $workFinish->diff($lastWorkStart);
                $workSeconds = $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;

                //休憩時間
                $lastRests = Rest::where('work_id', $lastWork->id)->get();
                foreach ($lastRests as $lastRest) {
                    $restFinish = Carbon::parse($lastRest->rest_finish);
                    $restStart = Carbon::parse($lastRest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $lastRestSeconds = 0;
                    $lastRestSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                }
                $restSeconds = 0;
                foreach ($user->rest as $rest) {
                    $restFinish = Carbon::parse($rest->rest_finish);
                    $restStart = Carbon::parse($rest->rest_start);
                    $diff = $restFinish->diff($restStart);
                    $restSeconds += $diff->days * 86400 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
                    $test = $restSeconds;
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
        $dates = Work::dateGroup()->pluck('date')->toArray();

        $index = array_search($request->date, $dates) + 1;
        $this->exist($index, $dates);

        return redirect('/attendance');
    }

    public function prev(Request $request)
    {
        $dates = Work::dateGroup()->pluck('date')->toArray();

        $index = array_search($request->date, $dates) - 1;
        $this->exist($index, $dates);

        return redirect('/attendance');
    }


    //indexが存在したら処理を実行するメソッド
    public function exist($index, $dates)
    {
        if (array_key_exists($index, $dates)) {
            session()->put('index', $index);
        }
    }
}
