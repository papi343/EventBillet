<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketScanController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {}

    /**
     * Scan a ticket using encrypted QR data.
     * Checks TicketPolicy@scan so ONLY the event organizer can scan.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);
 
        try {
            $ticket = $this->ticketService->findTicketFromQrData($request->input('qr_data'));

            // Check TicketPolicy@scan
            Gate::authorize('scan', $ticket);

            $scannedTicket = $this->ticketService->scanTicket($ticket);

            return response()->json([
                'success' => true,
                'message' => 'Ticket scanned successfully',
                'data' => new TicketResource($scannedTicket->load(['event', 'user'])),
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only the event organizer can scan this ticket',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Single-action invoke option if route is defined directly to controller.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->scan($request);
    }
}
