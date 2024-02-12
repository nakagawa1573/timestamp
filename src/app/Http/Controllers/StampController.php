<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;
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
        $latestRest = Rest::where('user_id', $user->id)->latest()->first();
        if (empty($latestWork->work_finish) && Work::where('user_id', $user->id)->count() > 0) {
            session()->put('work_id', $latestWork->id);
        }
        if (empty($latestRest->rest_finish) && Rest::where('user_id', $user->id)->count() > 0) {
            session()->put('rest_id', $latestRest->id);
        }
        return view('index', compact('user', 'work'));
    }

    //勤務開始処理
    public function startWork(Request $request)
    {
        $user= User::find($request->user_id);
        $latestWork = Work::where('user_id', $request->user_id)->latest()->first();
        if (empty($latestWork) || !empty($latestWork->work_finish)) {
            Work::create(['user_id' => $request->user_id, 'work_start' => now()]);
            $user->update(['status' => $request->status]);
            return redirect('/')->with('message', '勤務を開始しました');
        } else {
            return redirect('/')->with('message', '前回の勤務が終了していません');
        }
    }
    //勤務終了処理
    public function finishWork(WorkRequest $request)
    {
        $user = User::find($request->user_id);
        $startDate = Work::where('id', $request->id)->value('work_start');
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $now = Carbon::now();
        $rests = Rest::where('user_id', $request->user_id)->where('work_id', null)->get();
        $nulls = Rest::where('user_id', $request->user_id)->where('rest_finish', null)->get();
        if ($nulls->count() !== 0) {
            return redirect('/')->with('message', '休憩を終了してください')->with('nulls', $nulls);
        } else {
            try {
                DB::transaction(function () use ($request, $startDate, $now, $rests, $user) {
                    if ($startDate == $now->format('Y-m-d')) {
                        Work::find($request->id)->update(['work_finish' => $now]);
                    } else {
                        $work = Work::create(['user_id' => $request->user_id, 'work_finish' => $now]);
                        $workId = $work->id;
                        foreach ($rests as $rest) {
                            $rest->work_id = $workId;
                            $rest->save();
                        }
                    }
                    $user->update(['status' => $request->status]);
                    session()->put('work_id', null);
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
        $user = User::find($request->user_id);
        $latestRest = Rest::where('user_id', $request->user_id)->latest()->first();
        if (empty($latestRest) || !empty($latestRest->rest_finish)) {
            Rest::create(['user_id' => $request->user_id, 'work_id' => $request->work_id, 'rest_start' => now()]);
            $user->update(['status' => $request->status]);
            return redirect('/')->with('message', '休憩を開始しました');
        } else {
            return redirect('/')->with('message', '前回の休憩が終了していません');
        }
    }
    //休憩終了処理
    public function finishRest(RestRequest $request)
    {
        $user = User::find($request->user_id);
        $startDate = Rest::where('id', $request->id)->value('rest_start');
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $now = Carbon::now();
        $startOfDay = Carbon::now()->startOfDay();
        session()->put('startDate', $startDate);
        try {
            DB::transaction(function () use ($request, $startDate, $now, $startOfDay, $user) {
                if ($startDate == $now->format('Y-m-d')) {
                    Rest::find($request->id)->update(['rest_finish' => $now]);
                } else {
                    Rest::find($request->id)->update(['rest_finish' => $startOfDay]);
                    Rest::create(['user_id' => $request->user_id, 'work_id' => null, 'rest_start' => $startOfDay, 'rest_finish' => $now]);
                }
                $user->update(['status' => $request->status]);
                session()->put('rest_id', null);
            });
            return redirect('/')->with('message', '休憩を終了しました');
        } catch (\Exception $e) {
            return redirect('/')->with('message', '処理に失敗しました');
        }
    }
}
