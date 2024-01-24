<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserPutCurrentRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserGetCurrentResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            if (!$this->checkGender($data["gender"])) {
                throw new HttpResponseException(response([
                    "error" => "Invalid Gender"
                ], 400));
            }
    
            if (User::lockForUpdate()->where("username", $data["username"])->count() == 1) {
                throw new HttpResponseException(response([
                    "error" => "Username Already Registered"
                ], 400));
            }
    
            $user = new User($data);
            $user->password = Hash::make($data["password"]);
            $user->save();
    
            DB::commit();

            return new MessageResource("Success Register User", 201);
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

    public function getCurrent(): UserGetCurrentResource {
        try {
            $user = Auth::user();
    
            return new UserGetCurrentResource($user);
        } catch (\Exception $e) {
            throw new HttpResponseException(response([
                "error" => "Internal Server Error"
            ], 500));
        }
    }

    public function putCurrent(UserPutCurrentRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $user = Auth::user();
    
            if (isset($data["fullname"])) {
                $user->fullname = $data['fullname'];
            }
        
            if (isset($data["gender"])) {
                if (!$this->checkGender($data["gender"])) {
                    throw new HttpResponseException(response([
                        "error" => "Invalid Gender"
                    ], 400));
                }
        
                $user->gender = $data['gender'];
            }
        
            /** @var \App\Models\User $user **/
            $user->save();

            DB::commit();

            return new MessageResource("Success Edit Profile", 200);
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
    
    public function changeCurrentPassword(UserChangePasswordRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $user = Auth::user();
        
            if ($data["old_password"] == $data["new_password"]) {
                throw new HttpResponseException(response([
                    "error" => "Old and New Password must be Different"
                ], 400));
            }
        
            if (!Hash::check($data["old_password"], $user->password)) {
                throw new HttpResponseException(response([
                    "error" => "Old Password is Incorrect"
                ], 400));
            }
            
            $user->password = Hash::make($data["new_password"]);
        
            /** @var \App\Models\User $user **/
            $user->save();

            DB::commit();

            return new MessageResource("Success Change Password", 200);
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

    // PROTECTED METHOD

    protected function checkGender($gender) {
        if ($gender != "MALE" && $gender != "FEMALE") {
            return false;
        } else {
            return true;
        }
    }
}
