<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\BookSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\ParallelTesting;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class BookTest extends TestCase
{
    // POST /book/offline
    public function testBookOfflineSuccess(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $this->post("/api/book/offline", [
            "table_code" => "Table_1",
        ])->assertStatus(200)->assertJson([
            "message" => "Success Book Table"
        ]);
    }

    // POST /book/online
    public function testBookOnlineSuccess(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $user = $this->loginMember();
        $token = $user->token;
        $user_id = $user->id;

        $this->post("/api/book/online", [
            "table_code" => "Table_1",
            "user_id" => $user_id,
            "date" => "2024-03-03 18:00:00",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Book Table"
        ]);
    }

    // PROTECTED METHOD
    protected function login () {
        $this->post("/api/auth/login", [
            "username" => "admin",
            "password" => "admin",
        ])->assertStatus(200);
        
        $user = User::where('username', 'admin')->first();
        assertNotNull($user->token);

        return $user;
    }

    protected function loginMember () {
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angesti",
        ])->assertStatus(200);
        
        $user = User::where('username', 'farandi')->first();
        assertNotNull($user->token);

        return $user;
    }
}
