<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        protected TicketRepository $ticketRepository,
        protected CustomerService $customerService
    ) {
    }

    public function createNewTicket(array $data, array $files = []): Ticket
    {
        return DB::transaction(function () use ($data, $files) {
            $customer = $this->customerService->findOrCreate(
                $data['name'] ?? null,
                $data['phone'],
                $data['email']
            );

            $ticket = $this->ticketRepository->createTicket($customer, [
                'subject' => $data['subject'],
                'text' => $data['text'],
            ]);

            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $ticket->addMedia($file)->toMediaCollection('attachments');
                }
            }

            return $ticket;
        });
    }

    public function getTicketStatistics(): array
    {
        return $this->ticketRepository->getStatistics();
    }
}
