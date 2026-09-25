<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {}

    /**
     * Display a listing of tickets accessible by the authenticated user.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Ticket::class);

        $user = $request->user();

        $query = Ticket::with(['event', 'user', 'order']);

        if ($user->role === UserRole::Organiser) {
            $query->whereHas('event', function ($q) use ($user) {
                $q->where('organizer_id', $user->id);
            });
        } elseif ($user->role === UserRole::Participant) {
            $query->where('user_id', $user->id);
        }

        $tickets = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => TicketResource::collection($tickets)
        ], 200);
    }

    /**
     * Display details of a specific ticket.
     */
    public function show(Ticket $ticket): JsonResponse
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['event', 'user', 'order']);

        return response()->json([
            'success' => true,
            'data' => new TicketResource($ticket),
        ], 200);
    }
}
