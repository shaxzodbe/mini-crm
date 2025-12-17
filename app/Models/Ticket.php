<?php

namespace App\Models;

use App\Enums\TicketStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'customer_id',
        'subject',
        'text',
        'status',
        'manager_response_at',
    ];

    protected $casts = [
        'manager_response_at' => 'datetime',
        'status' => TicketStatusEnum::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    public function scopeCreatedBetween(Builder $query, Carbon $from, Carbon $to): void
    {
        $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeCreatedAfter(Builder $query, Carbon $date): void
    {
        $query->where('created_at', '>=', $date);
    }
}
