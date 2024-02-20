<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampsTest extends TestCase
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

    //勤務開始処理
    public function testWorkStart(): void
    {
        $user = $this->loginUser();
        //勤務開始
        $response = $this->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '勤務を開始しました');
    }

    //勤務終了処理
    public function testWorkFinish()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        //勤務終了
        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '' ,
            'status' => '勤務外'
        ]);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '勤務を終了しました');
    }

    //休憩開始処理
    public function testRestStart()
    {

        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        //休憩開始
        $response =  $this->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を開始しました');
    }

    //休憩終了処理
    public function testRestFinish()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        //休憩終了
        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を終了しました');
    }

    //休憩の繰り返し
    public function testRestAgain()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        sleep(1);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        sleep(1);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を終了しました');
    }

    //一連の流れのテスト
    public function testAllFinish()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '' ,
            'status' => '勤務外'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '勤務を終了しました');
    }

    //勤務開始を連続で押したときのエラー
    public function testWorkStartError()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $response = $this->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '前回の勤務が終了していません');
    }

    //勤務を開始した後休憩終了を押したときのエラー
    public function testWorkStartRestFinishError()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('id');
    }

    //最初に休憩開始を押したときのエラー
    public function testRestStartError()
    {
        $user = $this->loginUser();

        $response =  $this->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('work_id');
    }

    //最初に休憩終了を押したときのエラー
    public function testRestFinishError()
    {
        $user = $this->loginUser();

        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('id', 'work_id');
    }

    //最初に勤務終了を押したときのエラー
    public function testWorkFinishError()
    {
        $user = $this->loginUser();

        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' => session('work_id') ?? '' ,
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('id');
    }

    //勤務開始した後、休憩開始を連続で押したときの処理
    public function testRestStartAgainError()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        sleep(1);
        $response =  $this->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '前回の休憩が終了していません');
    }

    //勤務開始->休憩開始を押した後に勤務開始を押したときのエラー
    public function testWorkStartAgainError()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        sleep(1);
        $response = $this->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '前回の勤務が終了していません');
    }

    //勤務を終了押した後に勤務終了を押す
    public function testWorkFinishAgainError()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);

        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' => session('work_id') ?? '' ,
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を終了してください');
    }

    //勤務終了処理の失敗
    public function testWorkFinishCatch()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        DB::shouldReceive('transaction')->andThrow(new \Exception());
        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' => session('work_id') ?? '' ,
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('message', '処理に失敗しました');
    }

    //休憩終了処理の失敗
    public function testRestFinishCatch()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        DB::shouldReceive('transaction')->andThrow(new \Exception('Test Exception'));
        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('message', '処理に失敗しました');
    }

    //勤務終了が日を跨いだ時の処理
    public function testWorkFinishNewDay()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $nextDay = Carbon::now()->addDay();
        Carbon::setTestNow($nextDay);
        //勤務終了
        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' =>  session('work_id') ?? '' ,
            'status' => '勤務外'
        ]);
        $works = Work::where('user_id', $user->id)->get();
        foreach ($works as $work) {
            echo $work;
        }

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '勤務を終了しました');
    }

    //休憩終了日が日を跨いだ時の処理
    public function testRestFinishNewDay()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $time = Carbon::parse('2024-01-01 23:00:00');
        Carbon::setTestNow($time);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);

        $time = Carbon::parse('2024-01-02 01:00:00');
        Carbon::setTestNow($time);
        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $rests = Rest::where('user_id', $user->id)->get();
        foreach ($rests as $rest) {
            echo $rest;
        }
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を終了しました');
    }

    //勤務開始した後、再ログインして前回の勤務を引き継ぐ処理
    public function testWorkContinue()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/logout', []);
        $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->patch('/', [
            'user_id' => $user->id,
            'id' =>  session('work_id') ?? '' ,
            'status' => '勤務外'
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '勤務を終了しました');
    }

    //休憩開始した後、再ログインして前回の休憩を引き継ぐ処理
    public function testRestContinue()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '' ,
            'rest_start' => now(),
            'status' => '休憩中'
        ]);

        $this->followingRedirects()->post('/logout', []);
        $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('message', '休憩を終了しました');
    }
}
