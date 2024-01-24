<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TableAvailableResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($table) {
            return [
                "table_code" => $table->table_code,
            ];
        });
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(200);
        $response->setData($this->toArray($request));
    }
}
