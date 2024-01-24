<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/user/register", [UserController::class, "register"]);

Route::post("/auth/login", [AuthController::class, "login"]);

Route::get("/table/available", [TableController::class, "available"]);

Route::post("/book/offline", [BookController::class, "bookOffline"]);

Route::middleware(AuthAdminMiddleware::class)->group(function () {
  Route::post("/auth/logout-admin", [AuthController::class, "logout"]);

  Route::patch("/table/check-in", [TableController::class, "checkIn"]);
  Route::patch("/table/check-out", [TableController::class, "checkOut"]);
});

Route::middleware(AuthMiddleware::class)->group(function () {
  Route::post("/auth/logout-member", [AuthController::class, "logout"]);

  Route::post("/book/online", [BookController::class, "bookOnline"]);

  Route::get("/user/current", [UserController::class, "getCurrent"]);
  Route::put("/user/current", [UserController::class, "putCurrent"]);
  Route::patch("/user/current/change-password", [UserController::class, "changeCurrentPassword"]);
});
