<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("token", 100)->nullable()->unique("users_token_unique");
            $table->dateTime("token_expiry")->nullable();
            $table->boolean("is_admin")->nullable();
            $table->string("username", 100)->nullable(false)->unique("users_username_unique");
            $table->string("password", 100)->nullable(false);
            $table->string("fullname", 100)->nullable(false);
            $table->string("gender", 6)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
