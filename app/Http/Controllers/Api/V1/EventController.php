<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Requests\Api\V1\UpdateEventRequest;
use App\Http\Resources\Api\V1\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with(['organizer', 'category'])
            ->latest()
            ->paginate(15);

        return response()->json([
            "success" => true,
            "data" => EventResource::collection($events),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $event = $this->eventService->create(
            $request->validated(),
            $request->user(),
            $request->file('image')
        );

        $event->load(['organizer', 'category']);

        return response()->json([
            "success" => true,
            "data" => new EventResource($event),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with(['organizer', 'category'])->findOrFail($id);

        return response()->json([
            "success" => true,
            "data" => new EventResource($event),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        $event = Event::findOrFail($id);

        $updatedEvent = $this->eventService->update(
            $event,
            $request->validated(),
            $request->file('image')
        );

        $updatedEvent->load(['organizer', 'category']);

        return response()->json([
            "success" => true,
            "data" => new EventResource($updatedEvent),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $this->eventService->delete($event);

        return response()->json([
            "success" => true,
            "message" => "Event deleted successfully",
        ], 200);
    }
}
