<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableAvailableRequest;
use App\Http\Requests\TableCheckRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\TableAvailableResource;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function available(TableAvailableRequest $request): TableAvailableResource {
        try {
            $data = $request->validated();
    
            $date = Carbon::now();
            if(isset($data['date'])) {
                $date = Carbon::parse($data['date']);
            }
            
            $availableTables = Table::whereDoesntHave('books', function ($query) use ($date) {
                $query->whereDate('booked_date', $date->format('Y-m-d'));
            })->get();
    
            return new TableAvailableResource($availableTables);
        } catch (\Exception $e) {
            throw new HttpResponseException(response([
                "error" => "Internal Server Error"
            ], 500));
        }
    }

    public function checkIn(TableCheckRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $table = Table::where("table_code", $data["table_code"])->lockForUpdate()->first();
    
            if (!$table) {
                throw new HttpResponseException(response([
                    "error" => "Table not found"
                ], 404));
            } else if ($table->in_use == true) {
                throw new HttpResponseException(response([
                    "error" => "Table is already Checked In"
                ], 400));
            }
    
            $table->update(['in_use' => true]);
    
            DB::commit();

            return new MessageResource("Success Checkin User", 200);
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

    public function checkOut(TableCheckRequest $request): MessageResource {
        try {
            $data = $request->validated();
            DB::beginTransaction();

            $table = Table::where("table_code", $data["table_code"])->lockForUpdate()->first();
    
            if (!$table) {
                throw new HttpResponseException(response([
                    "error" => "Table not found"
                ], 404));
            } else if ($table->in_use == false) {
                throw new HttpResponseException(response([
                    "error" => "Table is already Checked Out"
                ], 400));
            }
    
            $table->update(['in_use' => false]);
    
            DB::commit();

            return new MessageResource("Success Checkout User", 200);
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
