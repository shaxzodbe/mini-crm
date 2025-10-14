<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'period' => [
                'daily' => $this->resource['daily'],
                'weekly' => $this->resource['weekly'],
                'monthly' => $this->resource['monthly'],
            ],
            'total_message' => 'Number of applications for the specified periods.',
        ];
    }
}
