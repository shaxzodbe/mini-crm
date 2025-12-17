<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketStatisticsResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createNewTicket($request->validated(), $request->file('files') ?? []);
        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);

    }

    public function statistics(): TicketStatisticsResource
    {
        $stats = $this->ticketService->getTicketStatistics();

        return new TicketStatisticsResource($stats);
    }
}
