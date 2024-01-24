<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGetCurrentResource extends JsonResource
{
    public function toResponse($request)
    {
        return response()->json([
            "username" => $this->username,
            "fullname" => $this->fullname,
            "gender" => $this->gender,
        ], 200);
    }
}
