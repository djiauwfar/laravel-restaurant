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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->nullable(true);
            $table->unsignedBigInteger("table_id")->nullable(false);
            $table->dateTime("booked_date")->nullable(false);
            $table->timestamps();

            $table->foreign("user_id")->on("users")->references("id");
            $table->foreign("table_id")->on("tables")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
