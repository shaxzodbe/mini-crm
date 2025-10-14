<?php

namespace App\Http\Controllers\Manager;

use App\Enums\TicketStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TicketController extends Controller
{
    protected TicketRepository $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function index(Request $request)
    {
        $tickets = $this->ticketRepository->getFilteredTickets($request->all());
        $statuses = TicketStatusEnum::cases();

        return view('manager.tickets.index', [
            'tickets' => $tickets,
            'filters' => $request->all(),
            'statuses' => $statuses,
        ]);
    }

    public function show(Ticket $ticket)
    {
        $statuses = TicketStatusEnum::cases();
        return view('manager.tickets.show', compact('ticket', 'statuses'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => ['required', 'string', new Enum(TicketStatusEnum::class)]]);

        $ticket->status = $request->status;
        $ticket->save();

        return redirect()->route('manager.tickets.show', $ticket)
            ->with('success', 'Статус заявки обновлен.');
    }
}
