<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'day' => $this->resource['day'] ?? 0,
                'week' => $this->resource['week'] ?? 0,
                'month' => $this->resource['month'] ?? 0,
            ],
        ];
    }
}
