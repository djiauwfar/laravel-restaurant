<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\BookSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class TableTest extends TestCase
{
    // POST >> /table/available
    public function testAvailableNoQuerySuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $this->get("/api/table/available")->assertStatus(200)->assertJson([
            [
                "table_code" => "Table_1"
            ],
            [
                "table_code" => "Table_2"
            ],
            [
                "table_code" => "Table_3"
            ],
            [
                "table_code" => "Table_4"
            ],
            [
                "table_code" => "Table_5"
            ]
        ]);
    }

    public function testAvailableWithQuerySuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $this->get("/api/table/available?date=2030-01-01 19:30:00")->assertStatus(200)->assertJson([
            [
                "table_code" => "Table_2"
            ],
            [
                "table_code" => "Table_3"
            ],
            [
                "table_code" => "Table_4"
            ],
            [
                "table_code" => "Table_5"
            ]
        ]);
    }

    public function testAvailableWithQueryValidationError(): void
    {
        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $this->get("/api/table/available?date=HAHA")->assertStatus(400);
    }

    // PATCH >> /table/check-in
    public function testCheckInSuccess(): void
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
        ])->assertStatus(200);

        Carbon::setTestNow(Carbon::parse("2024-03-03 18:30:00"));

        $user = $this->login();
        $token = $user->token;

        $this->patch("/api/table/check-in", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Checkin User"
        ]);
    }

    public function testCheckInValidationError(): void
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
        ])->assertStatus(200);

        Carbon::setTestNow(Carbon::parse("2024-03-03 18:30:00"));

        $user = $this->login();
        $token = $user->token;

        $this->patch("/api/table/check-in", [

        ], [
            "Authorization" => $token,
        ])->assertStatus(400);
    }

    public function testCheckInNotFoundError(): void
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
        ])->assertStatus(200);

        Carbon::setTestNow(Carbon::parse("2024-03-03 18:30:00"));

        $user = $this->login();
        $token = $user->token;

        $this->patch("/api/table/check-in", [
            "table_code" => "Table_10",
        ], [
            "Authorization" => $token,
        ])->assertStatus(404)->assertJson([
            "error" => "Table not found"
        ]);
    }

    public function testCheckInAlreadyCheckedInError(): void
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
        ])->assertStatus(200);

        Carbon::setTestNow(Carbon::parse("2024-03-03 18:30:00"));

        $user = $this->login();
        $token = $user->token;

        $this->patch("/api/table/check-in", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Checkin User"
        ]);

        $this->patch("/api/table/check-in", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(400)->assertJson([
            "error" => "Table is already Checked In"
        ]);
    }

    // PATCH >> /table/check-out
    public function testCheckOutSuccess(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $token = $this->login()->token;

        $this->post("/api/book/offline", [
            "table_code" => "Table_1",
        ])->assertStatus(200);

        $this->patch("/api/table/check-out", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Checkout User"
        ]);
    }

    public function testCheckOutValidationError(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $token = $this->login()->token;

        $this->post("/api/book/offline", [
            "table_code" => "Table_1",
        ])->assertStatus(200);

        $this->patch("/api/table/check-out", [

        ], [
            "Authorization" => $token,
        ])->assertStatus(400);
    }

    public function testCheckOutNotFoundError(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $token = $this->login()->token;

        $this->post("/api/book/offline", [
            "table_code" => "Table_1",
        ])->assertStatus(200);

        $this->patch("/api/table/check-out", [
            "table_code" => "Table_10",
        ], [
            "Authorization" => $token,
        ])->assertStatus(404)->assertJson([
            "error" => "Table not found"
        ]);
    }

    public function testCheckOutAlreadyCheckedOutError(): void
    {
        Carbon::setTestNow(Carbon::parse("2024-01-01 18:00:00"));

        $this->seed([UserSeeder::class]);
        $this->seed([TableSeeder::class]);
        $this->seed([BookSeeder::class]);

        $token = $this->login()->token;

        $this->post("/api/book/offline", [
            "table_code" => "Table_1",
        ])->assertStatus(200);

        $this->patch("/api/table/check-out", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Checkout User"
        ]);;

        $this->patch("/api/table/check-out", [
            "table_code" => "Table_1",
        ], [
            "Authorization" => $token,
        ])->assertStatus(400)->assertJson([
            "error" => "Table is already Checked Out"
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
