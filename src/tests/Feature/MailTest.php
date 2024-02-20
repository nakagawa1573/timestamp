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
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Queue;
use App\Actions\Fortify\UpdateUserProfileInformation;

class MailTest extends TestCase
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

    public function testVerifyEmail()
    {
        Mail::fake();
        $name = '山田太郎';
        $email = 'test@test.com';
        $password = 'password';
        $password_confirmation = 'password';

        $response = $this->followingRedirects()->post('/register', ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $password_confirmation]);
        $user = User::where('email', 'test@test.com')->first();
        $user->sendEmailVerificationNotification();
        Mail::assertSent(MustVerifyEmail::class);
    }

    public function testWeb()
    {
        $user = User::factory()->create();
        $response = $this->followingRedirects()->actingAs($user)
                    ->get('/');
        $response->assertStatus(200);
        $content = $response->getContent();
        Log::info($content);
    }
}
