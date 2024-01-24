<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    protected $message;
    protected $code;

    public function __construct($message, $code)
    {
        parent::__construct($message);
        $this->message = $message;
        $this->code = $code;
    }

    public function toResponse($request)
    {
        return response()->json([
            'message' => $this->message,
        ], $this->code);
    }
}
