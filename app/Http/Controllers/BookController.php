<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookOfflineRequest;
use App\Http\Requests\BookOnlineRequest;
use App\Http\Resources\MessageResource;
use App\Models\Book;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function bookOffline(BookOfflineRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $date = Carbon::now();
            if (!$date->between($date->copy()->setTime(17, 0, 0), $date->copy()->setTime(20, 0, 0))) {
                throw new HttpResponseException(response([
                    "error" => "Table can only be Booked between 17:00 and 20:00 Singapore Time"
                ], 400));
            }

            $table = Table::where("table_code", $data["table_code"])->lockForUpdate()->first();
            if (!$table) {
                throw new HttpResponseException(response([
                    "error" => "Table Not Found"
                ], 404));
            }

            $books = $table->books()->whereDate('booked_date', '=', $date->toDateString())->get();
            if (!$books->isEmpty() && $table->in_use == true && $table->updated_at < $date) {
                throw new HttpResponseException(response([
                    "error" => "Table Already Booked on Today"
                ], 400));
            }
            
            $table->in_use = true;
            $table->save();
            
            $book = new Book();
            $book->user_id = null;
            $book->table_id = $table->id;
            $book->booked_date = $date;
            $book->save();
            
            DB::commit();
    
            return new MessageResource("Success Book Table", 200);    
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

    public function bookOnline(BookOnlineRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();
            
            $date = Carbon::parse($data["date"]);
            if ($date < Carbon::now()) {
                throw new HttpResponseException(response([
                    "error" => "The Table cannot be Booked for Today or any Past Date"
                ], 400));
            } else if (!$date->between($date->copy()->setTime(17, 0, 0), $date->copy()->setTime(20, 0, 0))) {
                throw new HttpResponseException(response([
                    "error" => "Table can only be Booked between 17:00 and 20:00 Singapore Time"
                ], 400));
            }            

            $table = Table::where("table_code", $data["table_code"])->lockForUpdate()->first();
            if (!$table) {
                throw new HttpResponseException(response([
                    "error" => "Table Not Found"
                ], 404));
            }

            $books = $table->books()->whereDate('booked_date', '=', $date->toDateString())->get();
            if (!$books->isEmpty() && $table->in_use == true && $table->updated_at < $date) {
                throw new HttpResponseException(response([
                    "error" => "Table Already Booked on the Specific Date"
                ], 400));
            }

            $book = new Book();
            $book->user_id = Auth::user()->id;
            $book->table_id = $table->id;
            $book->booked_date = $date;
            $book->save();

            DB::commit();
    
            return new MessageResource("Success Book Table", 200);    
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
}
