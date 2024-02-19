<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('work', 'rest')
            ->nameSearch($request->keyword)
            ->statusSearch($request->status)
            ->Paginate(10);
            
        $status = $request->status;
        $keyword = $request->keyword;

        $lastWorks = [];
        foreach ($users as $user) {
            $works = Work::where('user_id', $user->id)->latest()->get();
            if ($works->count() > 0) {
                $lastWork = $works->first();
                $skipWork = $works->skip(1)->first();
                $rest = Rest::where('user_id', $user->id)->latest()->first();
                $lastWork = Carbon::parse($user->updated_at)->format('Y-m-d');
                $lastWorks[$user->id] = $lastWork;
            } else {
                $lastWorks[$user->id] = null;
            }
        }
        session(['last_work' => $lastWorks]);
        return view('users', compact('users', 'status', 'keyword'));
    }
}
