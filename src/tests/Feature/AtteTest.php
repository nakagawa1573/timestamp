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
        return $user;
    }

    //勤務時間、休憩時間が日を跨いだ時の表示。結果はログに出力
    public function testWorkRestNewDay(): void
    {
        $user = $this->loginUser();

        $time = Carbon::parse('2024-01-01 20:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/', ['user_id' => $user->id,]);

        $time = Carbon::parse('2024-01-01 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
        ]);

        $time = Carbon::parse('2024-01-02 01:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
        ]);
        $time = Carbon::parse('2024-01-02 08:00:00');
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
}
