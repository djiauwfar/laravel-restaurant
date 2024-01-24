<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    // POST >> /user/register
    public function testRegisterSuccess () {
        $this->post("/api/user/register", [
            "username" => "farandi",
            "password" => "angesti",
            "fullname" => "Farandi Angesti",
            "gender" => "MALE"
        ])->assertStatus(201)->assertJson([
            "message" => "Success Register User"
        ]);
    }

    public function testRegisterValidationError () {
        $this->post("/api/user/register", [
            "username" => "farandi",
            "password" => "angesti",
            "fullname" => "",
            "gender" => "MALE",
        ])->assertStatus(400);
    }

    public function testRegisterInvalidGenderError () {
        $this->post("/api/user/register", [
            "username" => "farandi",
            "password" => "angesti",
            "fullname" => "Farandi Angesti",
            "gender" => "MALEZ",
        ])->assertStatus(400)->assertJson([
            "error" => "Invalid Gender"
        ]);
    }

    public function testRegisterUsernameAlreadyExistError () {
        $this->testRegisterSuccess();
        $this->post("/api/user/register", [
            "username" => "farandi",
            "password" => "angesti",
            "fullname" => "Farandi Angesti",
            "gender" => "MALE",
        ])->assertStatus(400)->assertJson([
            "error" => "Username Already Registered"
        ]);
    }

    // GET >> /user/current
    public function testGetCurrentSuccess () {
        $token = $this->login();

        $this->get("/api/user/current", [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "username" => "farandi",
            "fullname" => "Farandi Angesti",
            "gender" => "MALE"
        ]);
    }

    // PUT >> /user/current
    public function testPutCurrentSuccess () {
        $token = $this->login();

        $this->put("/api/user/current", [
            "fullname" => "farandiz",
            "gender" => "FEMALE",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "message" => "Success Edit Profile"
        ]);
    }

    public function testPutCurrentValidationError () {
        $token = $this->login();

        $this->put("/api/user/current", [
            "fullname" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
            "gender" => "FEMALE",
        ], [
            "Authorization" => $token,
        ])->assertStatus(400);
    }

    public function testPutCurrentInvalidGenderError () {
        $token = $this->login();

        $this->put("/api/user/current", [
            "fullname" => "farandiz",
            "gender" => "FEMAZ",
        ], [
            "Authorization" => $token,
        ])->assertStatus(400)->assertJson([
            "error" => "Invalid Gender"
        ]);
    }

    // PATCH >> /user/current/change-password
    public function testChangeCurrentPasswordSuccess () {
        $token = $this->login();

        $this->patch("/api/user/current/change-password", [
            "old_password" => "angesti",
            "new_password" => "angestiz",
        ], [
            "Authorization" => $token
        ])->assertStatus(200)->assertJson([
            "message" => "Success Change Password"
        ]);
    }

    public function testChangeCurrentPasswordValidationError () {
        $token = $this->login();

        $this->patch("/api/user/current/change-password", [
            "old_password" => "angesti",
            "new_password" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
        ], [
            "Authorization" => $token
        ])->assertStatus(400);
    }

    // PROTECTED METHOD
    protected function login () {
        $this->seed([UserSeeder::class]);
        $this->post("/api/auth/login", [
            "username" => "farandi",
            "password" => "angesti",
        ])->assertStatus(200);
        
        $user = User::where('username', 'farandi')->first();
        assertNotNull($user->token);

        return $user->token;
    }
}
