<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;

class UsersTest extends TestCase
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

    //ユーザー一覧の表示。結果はログで
    public function testUsers()
    {
        User::factory()->count(10)->create();
        $this->loginUser();
        $response = $this->followingRedirects()->get('/users');

        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //検索-完全一致
    public function testSearchFullPart()
    {
        User::factory()->count(10)->create();
        $user = $this->loginUser();
        $response = $this->get(route('users', ['keyword' => $user->name]));

        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //検索-一部一致
    public function testSearchPart()
    {
        User::factory()->count(10)->create();
        User::create([
            'name' => '山田 太郎',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);
        $response = $this->get(route('users', ['keyword' => '山田']));

        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //検索-セレクトボックス
    public function testSearchSelect(){
        User::factory()->count(10)->create();
        $user = $this->loginUser();
        $this->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $response = $this->get(route('users', ['status' => '勤務中']));
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //勤怠表ボタンテスト
    public function testButton()
    {
        $user = $this->loginUser();
        $response = $this->get(route('attendance.user', ['id' => $user->id]));

        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    //勤務中の表示
    public function testStatusWork()
    {
        $user = $this->loginUser();
        $this->post('/', ['user_id' => $user->id, 'status' => '勤務中']);

        $response = $this->followingRedirects()->get('/users');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //休憩中の表示
    public function testStatusRest()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $response = $this->followingRedirects()->get('/users');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //再び勤務中の表示
    public function testStatusWorkContinue()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $this->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $response = $this->followingRedirects()->get('/users');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
    //勤務外の表示
    public function testStatusWorkOut()
    {
        $user = $this->loginUser();
        $this->followingRedirects()->post('/', ['user_id' => $user->id, 'status' => '勤務中']);
        $this->followingRedirects()->post('/rest', [
            'user_id' => $user->id,
            'work_id' => session('work_id') ?? '',
            'rest_start' => now(),
            'status' => '休憩中'
        ]);
        $this->followingRedirects()->patch('/rest', [
            'user_id' => $user->id,
            'work_id' =>  session('work_id') ?? '',
            'id' => session('rest_id') ?? '',
            'status' => '勤務中'
        ]);
        $this->patch('/', [
            'user_id' => $user->id,
            'id' =>   session('work_id') ?? '',
            'status' => '勤務外'
        ]);
        $response = $this->followingRedirects()->get('/users');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
}
