<?php

namespace App\Repositories;

use App\Enums\TicketStatusEnum;
use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository
{
    public function createTicket(Customer $customer, array $data): Ticket
    {
        return $customer->tickets()->create([
            'subject' => $data['subject'],
            'text' => $data['text'],
            'status' => TicketStatusEnum::NEW,
        ]);
    }

    public function getRecentTicketByContact(string $phone, string $email, Carbon $since): ?Ticket
    {
        return Ticket::query()
            ->whereHas('customer', function ($query) use ($phone, $email) {
                $query->where('phone', $phone)->orWhere('email', $email);
            })
            ->where('created_at', '>=', $since)
            ->latest()
            ->first();
    }

    public function getFilteredTickets(array $filters): LengthAwarePaginator
    {
        $query = Ticket::query();

        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (!empty($filters['email'])) {
            $query->whereHas('customer', fn($q) => $q->where('email', 'like', '%' . $filters['email'] . '%'));
        }

        if (!empty($filters['phone'])) {
            $query->whereHas('customer', fn($q) => $q->where('phone', 'like', '%' . $filters['phone'] . '%'));
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        return $query->latest()->paginate(20);
    }
}
