<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where("username", "farandi")->first();
        $table = Table::where("table_code", "Table_1")->first();

        Book::create([
            "user_id" => $user->id,
            "table_id" => $table->id,
            "booked_date" => Carbon::parse("2030-01-01 18:00:00"),
        ]);
    }
}
