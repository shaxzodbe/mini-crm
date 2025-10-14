<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\CustomerRepository;
use App\Repositories\TicketRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    protected TicketRepository $ticketRepository;
    protected CustomerRepository $customerRepository;

    public function __construct(
        TicketRepository $ticketRepository,
        CustomerRepository $customerRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->customerRepository = $customerRepository;
    }

    public function createNewTicket(array $data, array $files = []): Ticket
    {
        try {
            return DB::transaction(function () use ($data, $files) {
                $customer = $this->customerRepository->findOrCreateByPhoneOrEmail(
                    $data['phone'],
                    $data['email'],
                    ['name' => $data['name'] ?? null]
                );

                $ticket = $this->ticketRepository->createTicket($customer, $data);

                if (!empty($files)) {
                    $this->attachFilesToTicket($ticket, $files);
                }

                return $ticket;
            });
        } catch (\Exception $e) {
            Log::error('Ticket creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    protected function attachFilesToTicket(Ticket $ticket, array $files): void
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $ticket->addMedia($file)
                ->toMediaCollection('attachments');
        }
    }

    public function getTicketStatistics(): array
    {
        $now = Carbon::now();

        return [
            'daily' => $this->calculateCountForPeriod($now->copy()->subDay(), $now),
            'weekly' => $this->calculateCountForPeriod($now->copy()->subWeek(), $now),
            'monthly' => $this->calculateCountForPeriod($now->copy()->subMonth(), $now),
        ];
    }

    protected function calculateCountForPeriod(Carbon $from, Carbon $to): int
    {
        return Ticket::query()
            ->createdBetween($from, $to)
            ->count();
    }
}
