<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;


class UserCreateTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function testUserCreate()
    {
        $response = $this->post('/register',[
            'name' => $this->faker->name(),
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(302);
    }

    public function testAllError()
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function testNameErrorRequired(): void
    {   //required
        $response = $this->post('/register', [
            'name' => ' ',
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['name']);
    }

    public function testNameErrorString()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->randomNumber(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['name']);
    }

    public function testNameErrorMax()
    {
        $response = $this->post('/register', [
            'name' => Str::random(192),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['name']);
    }

    public function testEmailErrorRequired()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => ' ',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['email']);
    }
    public function testEmailErrorEmailFilter()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => 'テスト@testcom',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function testEmailErrorEmailDns()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => 'test@gmail.moc',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function testEmailErrorUnique()
    {
        $user = User::factory()->create();
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function testPasswordErrorRequired()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => ' ',
            'password_confirmation' => ' ',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    public function testPasswordErrorMin()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'passwor',
            'password_confirmation' => 'passwor',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    public function testPasswordErrorMax()
    {
        $max = Str::random(192);
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => $max,
            'password_confirmation' => $max,
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    public function testPasswordErrorConfirmed()
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password1',
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}
