<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Resources\AuthLoginResource;
use App\Http\Resources\MessageResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): AuthLoginResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $user = User::where("username", $data["username"])->lockForUpdate()->first();
            if (!$user || !Hash::check($data["password"], $user->password)) {
                throw new HttpResponseException(response([
                    "error" => "Username or Password Wrong"
                ], 401));
            };
    
            $user->token = Str::uuid()->toString();
            $user->token_expiry = $user->is_admin == true ? Carbon::now()->addMonth() : Carbon::now()->addDay();
            $user->save();
    
            DB::commit();

            return new AuthLoginResource($user->token);
        } catch (HttpResponseException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response([
                "error" => "Internal Server Error"
            ], 500));
        }
    }

    public function logout(): MessageResource {
        try {
            DB::beginTransaction();

            $user = Auth::user();
        
            $user->token = null;
            $user->token_expiry = null;
            /** @var \App\Models\User $user **/
            $user->save();

            DB::commit();

            return new MessageResource("Success Logout", 200);    
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response([
                "error" => "Internal Server Error"
            ], 500));
        }
    }
}
