<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketStatisticsResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class TicketApiController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createNewTicket($request->validated(), $request->file('files') ?? []);

        return Response::json([
            'message' => 'Заявка успешно создана.',
            'ticket' => new TicketResource($ticket),
        ], 201);
    }

    public function statistics(): TicketStatisticsResource
    {
        $stats = $this->ticketService->getTicketStatistics();

        return new TicketStatisticsResource($stats);
    }
}
