<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class AuthTest extends TestCase
{
    // POST >> /auth/login
    public function testLoginSuccess () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angesti",
        ])->assertStatus(200);

        $user = User::where('username', 'farandi')->first();
        assertNotNull($user->token);
    }

    public function testLoginUsernameWrongError () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandiz",
            "password" => "angesti",
        ])->assertStatus(401)->assertJson([
            "error" => "Username or Password Wrong"
        ]);
    }

    public function testLoginPasswordWrongError () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angestiz",
        ])->assertStatus(401)->assertJson([
            "error" => "Username or Password Wrong"
        ]);
    }

    // POST >> /auth/logout-member
    public function testLogoutMemberSuccess () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angesti",
        ])->assertStatus(200);

        $user = User::where('username', 'farandi')->first();

        $this->post("/api/auth/logout-member", [],
        [
            "Authorization" => $user->token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Logout"
        ]);
    }

    public function testLogoutMemberWrongTypeError () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "admin",
            "password" => "admin",
        ])->assertStatus(200);

        $user = User::where('username', 'admin')->first();

        $this->post("/api/auth/logout-member", [],
        [
            "Authorization" => $user->token,
        ])->assertStatus(401)->assertJson([
            "error" => "Unauthorized"
        ]);
    }

    // POST >> /auth/logout-admin
    public function testLogoutAdminSuccess () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "admin",
            "password" => "admin",
        ])->assertStatus(200);

        $user = User::where('username', 'admin')->first();

        $this->post("/api/auth/logout-admin", [],
        [
            "Authorization" => $user->token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Logout"
        ]);
    }

    public function testLogoutAdminWrongTypeError () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angesti",
        ])->assertStatus(200);

        $user = User::where('username', 'farandi')->first();

        $this->post("/api/auth/logout-admin", [],
        [
            "Authorization" => $user->token,
        ])->assertStatus(401)->assertJson([
            "error" => "Unauthorized"
        ]);
    }    
}
