<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \Illuminate\Support\Facades\Log;

class AtteTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function loginUser()
    {
        $user = User::factory()->create();
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->actingAs($user);
        return $user;
    }

    //勤務時間、休憩時間が日を跨いだ時の表示。結果はログに出力
    public function testWorkRestNewDay(): void
    {
        $user = $this->loginUser();

        $time = Carbon::parse('2024-01-01 20:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $time = Carbon::parse('2024-01-01 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);

        $time = Carbon::parse('2024-01-02 01:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-02 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>  session('work_id') ?? '',
            'status' => '勤務外'
        ]);
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $content = $response->getContent();
        Log::info($content);
        $content = $this->followingRedirects()->post('/attendance/next', [
            'date' =>  $dates[session('index')] ?? ''
        ])->getContent();
        Log::info($content);
    }

    //勤務、休憩ともに同じ日に終わる。結果はログに出力
    public function testWorkRestToday()
    {
        $user = $this->loginUser();
        $time = Carbon::parse('2024-01-01 08:00:00');
        Carbon::setTestNow($time);

        $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

        $time = Carbon::parse('2024-01-01 12:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
        ]);

        $time = Carbon::parse('2024-01-01 14:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
        ]);

        $time = Carbon::parse('2024-01-01 20:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>  session('work_id') ?? '',
        ]);
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //最新の日付の状態で/nextをしても変化しないかテスト
    public function testNoNext()
    {
        $user = $this->loginUser();

        $time = Carbon::parse('2024-01-01 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

        $time = Carbon::parse('2024-01-01 17:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>  session('work_id') ?? '',
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
        $content = $this->followingRedirects()->post('/attendance/next', [
            'date' =>  $dates[session('index')] ?? ''
        ])->getContent();
        Log::info($content);
    }

    //prevが機能するかテスト
    // public function testPrev()
    // {
    //     $user = $this->loginUser();

    //     $time = Carbon::parse('2024-01-01 08:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

    //     $time = Carbon::parse('2024-01-01 17:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->patch('/', [
    //         'user_id' => $user->id,
    //         'id' =>  session('work_id') ?? '',
    //     ]);

    //     $time = Carbon::parse('2024-01-02 10:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

    //     $time = Carbon::parse('2024-01-02 20:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->patch('/', [
    //         'user_id' => $user->id,
    //         'id' =>  session('work_id') ?? '',
    //     ]);

    //     $this->get('/attendance');
    //     $this->followingRedirects()->post('/attendance/next', ['date' =>  $dates[session('index')] ?? '']);

    //     $response = $this->followingRedirects()->post('/attendance/prev', ['date' =>  $dates[session('index')] ?? '']);
    //     $response->assertStatus(200);
    //     dd(session('index'));
    //     $content = $response->getContent();
    //     Log::info($content);
    // }

    //最古の日付の状態で/prevをしても変化しないかテスト
    // public function testNoPrev()
    // {
    //     $user = $this->loginUser();

    //     $time = Carbon::parse('2024-01-01 08:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

    //     $time = Carbon::parse('2024-01-01 17:00:00');
    //     Carbon::setTestNow($time);
    //     $this->followingRedirects()->patch('/', [
    //         'user_id' => $user->id,
    //         'id' =>  session('work_id') ?? '',
    //     ]);

    //     $response = $this->get('/attendance');
    //     $response->assertStatus(200);
    //     $content = $response->getContent();
    //     Log::info($content);
    //     $content = $this->followingRedirects()->post('/attendance/prev', [
    //         'date' =>  $dates[session('index')] ?? ''
    //     ])->getContent();
    //     Log::info($content);
    // }

    //個人勤怠表ページ　同日に複数の勤務
    public function testWorks()
    {
        $user = $this->loginUser();

        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);
        sleep(1);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $response = $this->get(route('attendance.user', ['id' => $user->id]));
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //個人勤怠表ページ　>ボタンを押す
    public function testNextMonth()
    {
        $user = $this->loginUser();
        $this->get(route('attendance.user', ['id' => $user->id]));
        $date = Carbon::parse(session('date'))->format('Y-m-d');
        if ($date === null) {
            $date = Carbon::now()->format('Y-m-d');
        }
        $response = $this->followingRedirects()->post('/attendance/user-next', [
            'date' => $date,
            'id' => $user->id,
        ]);
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //個人勤怠表ページ　<ボタンを押す
    public function testPrevMonth()
    {
        $user = $this->loginUser();
        $this->get(route('attendance.user', ['id' => $user->id]));
        $date = Carbon::parse(session('date'))->format('Y-m-d');
        if ($date === null) {
            $date = Carbon::now()->format('Y-m-d');
        }
        $response = $this->followingRedirects()->post('/attendance/user-prev', [
            'date' => $date,
            'id' => $user->id,
        ]);
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //個人勤怠表ページ　実働日数、実働時間、総休憩時間の表示
    public function testTotal()
    {
        $user = $this->loginUser();

        $time = Carbon::parse('2024-01-01 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-01 09:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-01 12:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $time = Carbon::parse('2024-01-02 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-02 09:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-02 10:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-02 12:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $response = $this->get(route('attendance.user', ['id' => $user->id]));
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //個人勤怠表ページ　勤務と休憩がまだ終わっていないときの表示
    public function testUserWorkRest()
    {
        $user = $this->loginUser();
        $date = Carbon::parse(session('date'))->format('Y-m-d');
        if ($date === null) {
            $date = Carbon::now()->format('Y-m-d');
        }
        $time = Carbon::parse('2024-01-01 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-01 12:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $response = $this->get(route('attendance.user', ['id' => $user->id]));
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //個人勤怠表ページ　勤務日が日を跨いだ時の表示
    public function testNightShift()
    {
        $user = $this->loginUser();
        $date = Carbon::parse(session('date'))->format('Y-m-d');
        if ($date === null) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $time = Carbon::parse('2024-01-01 21:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-01 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-02 01:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-02 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $time = Carbon::parse('2024-01-02 20:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-02 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-03 01:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-03 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $time = Carbon::parse('2024-01-04 02:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-04 03:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-04 04:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-04 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);

        $time = Carbon::parse('2024-01-04 20:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $time = Carbon::parse('2024-01-04 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $time = Carbon::parse('2024-01-05 01:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $time = Carbon::parse('2024-01-05 08:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);
        $response = $this->get(route('attendance.user', ['id' => $user->id]));
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);

        $response = $this->get('/attendance');
        $content = $response->getContent();
        Log::info($content);

        //nextするにはこれがいる
        // $index = 0;
        // $works = Work::oldest()->get();
        // $dates = [];
        // foreach ($works as $work) {
        //     $date = isset($work->work_finish) ? Carbon::parse($work->work_finish)->format('Y-m-d') : Carbon::parse($work->work_start)->format('Y-m-d');
        //     $dates[] = $date;
        // }
        // $dates = array_unique($dates);
        // sort($dates);
        // if (array_key_exists($index, $dates)) {
        //     session()->put('index', $index);
        // }

        // $content = $this->followingRedirects()->post('/attendance/next', [
        //     'date' =>  $dates[session('index')] ?? ''
        // ])->getContent();
        // Log::info($content);

        // $content = $this->followingRedirects()->post('/attendance/next', [
        //     'date' =>  $dates[session('index')] ?? ''
        // ])->getContent();
        // Log::info($content);

        // $content = $this->followingRedirects()->post('/attendance/next', [
        //     'date' =>  $dates[session('index')] ?? ''
        // ])->getContent();
        // Log::info($content);

        // $content = $this->followingRedirects()->post('/attendance/next', [
        //     'date' =>  $dates[session('index')] ?? ''
        // ])->getContent();
        // Log::info($content);

        // $content = $this->followingRedirects()->post('/attendance/next', [
        //     'date' =>  $dates[session('index')] ?? ''
        // ])->getContent();
        // Log::info($content);
    }

}
