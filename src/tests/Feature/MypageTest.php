<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use \Illuminate\Support\Facades\Log;

class MypageTest extends TestCase
{
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

    public function testMypageView()
    {
        $this->loginUser();

        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }

    public function testAtteUser()
    {
        $user = $this->loginUser();

        $response = $this->get(route('attendance.user', ['id' => $user->id]));
        // $content = $response->getContent();
        // Log::info($content);
        $response->assertStatus(200);
    }
}
