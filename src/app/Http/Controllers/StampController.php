<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Rest;
use App\Http\Requests\RestRequest;
use App\Http\Requests\WorkRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $work = Work::with('rest')->get();
        $latestWork = Work::where('user_id', $user->id)->latest()->first();
        //前回の勤務が終了してなければ引き継ぐ
        if (empty($latestWork->work_finish) && Work::where('user_id', $user->id)->count() > 0) {
            $work_id['work_id'] = $latestWork->id;
            session()->put($work_id);
        }
        return view('index', compact('user', 'work'));
    }

    //勤務開始処理
    public function startWork(Request $request)
    {
        // $null = Work::where('user_id', $request->user_id)->where('work_finish', null)->first();
        $latestWork = Work::where('user_id', $request->user_id)->latest()->first();

        if (empty($latestWork) || !empty($latestWork->work_finish)) {
            Work::create(['user_id' => $request->user_id, 'work_start' => now()]);
            // $work = Work::where('user_id', $request->user_id)->latest()->first();
            // $work_id['work_id'] = $work->id;
            // session()->put($work_id);
            return redirect('/')->with('message', '勤務を開始しました');
        } else {
            return redirect('/')->with('message', '前回の勤務が終了していません');
        }
    }
    //勤務終了処理
    public function finishWork(WorkRequest $request)
    {
        $startDate = Work::where('id', $request->id)->value('work_start');
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $now = \Carbon\Carbon::now();
        // $now = Carbon::now()->addHours(24);
        $rests = Rest::where('user_id', $request->user_id)->where('work_id', null)->get();
        $nulls = Rest::where('user_id', $request->user_id)->where('rest_finish', null)->get();
        if ($nulls->count() !== 0) {
            return redirect('/')->with('message', '休憩を終了してください')->with('nulls',$nulls);
        } else {
            try {
                DB::transaction(function () use ($request, $startDate, $now, $rests) {
                    if ($startDate == $now->format('Y-m-d')) {
                        Work::find($request->id)->update(['work_finish' => $now]);
                    } else {
                        //word_idがnullのレコードにword_idを与える
                        $work = Work::create(['user_id' => $request->user_id, 'work_finish' => $now, 'created_at' => $now]);
                        $workId = $work->id;
                        foreach ($rests as $rest) {
                            $rest->work_id = $workId;
                            $rest->save();
                        }
                    }

                    foreach ($rests as $rest) {
                        $rest->rest_finish = now();
                        $rest->save();
                    }
                });
                return redirect('/')->with('message', '勤務を終了しました');
            } catch (\Exception $e) {
                return redirect('/')->with('message', '処理に失敗しました');
            }
        }
    }

    //休憩開始処理
    public function startRest(RestRequest $request)
    {
        try {
            $latestRest = Rest::where('user_id', $request->user_id)->latest()->first();
            if (empty($latestRest) || !empty($latestRest->rest_finish)) {
                DB::transaction(function () use ($request) {
                    Rest::create(['user_id' => $request->user_id, 'work_id' => $request->work_id, 'rest_start' => now()]);
                    $rest = Rest::where('user_id', $request->user_id)->latest()->first();
                    $restId['rest_id'] = $rest->id;
                    session()->put($restId);
                });
                return redirect('/')->with('message', '休憩を開始しました');
            } else {
                return redirect('/')->with('message', '前回の休憩が終了していません');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('message', '処理に失敗しました');
        }
    }
    //休憩終了処理
    public function finishRest(RestRequest $request)
    {
        $startDate = Rest::where('id', $request->id)->value('rest_start');
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $now = Carbon::now();
        $test = Carbon::now()->addHours(2);
        session()->put('startDate', $startDate);
        try {
            DB::transaction(function () use ($request, $startDate, $now, $test) {
                if ($startDate == $now->format('Y-m-d')) {
                    Rest::find($request->id)->update(['rest_finish' => $now]);
                } else {
                    //日を跨いだ時の処理
                    Rest::find($request->id)->update(['rest_finish' => $now->subDay()->endOfDay()]);
                    Rest::create(['user_id' => $request->user_id, 'work_id' => null, 'rest_start' => $now->startOfDay(), 'rest_finish' => $test]);
                }
                session()->put('rest_id', null);
            });

            return redirect('/')->with('message', '休憩を終了しました');
        } catch (\Exception $e) {
            return redirect('/')->with('message', '処理に失敗しました');
        }
    }
}
