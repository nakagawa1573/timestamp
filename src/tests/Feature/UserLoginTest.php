<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function testUserLogin(): void
    {
        $user = User::factory()->create();

        $this->get('/login')
            ->type($user->email, 'email')
            ->type('secret', 'password')
            ->press('ログイン')
            ->seePageIs('/');
    }
}
