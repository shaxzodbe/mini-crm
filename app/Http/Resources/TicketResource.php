<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'id' => $this->resource->id,
                'subject' => $this->resource->subject,
                'status' => $this->resource->status,
            ],
        ];
    }
}
