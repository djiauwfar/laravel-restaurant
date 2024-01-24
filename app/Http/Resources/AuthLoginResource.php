<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthLoginResource extends JsonResource
{
    protected $token;

    public function __construct($token)
    {
        parent::__construct($token);
        $this->token = $token;
    }

    public function toResponse($request)
    {
        return response()->json([
            'token' => $this->token,
        ], 200);
    }
}
