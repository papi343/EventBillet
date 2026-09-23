<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Enums\EventStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventService
{
    public function create(array $data, User $organiser, ?UploadedFile $image = null): Event
    {
        $data['organizer_id'] = $organiser->id;
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['available_seats'] = $data['total_seats'];
        $data['status'] = EventStatus::Draft;

        if ($image) {
            $data['image'] = $image->store('events', 'public');
        }

        return Event::create($data);
    }

    public function update(Event $event, array $data, ?UploadedFile $image = null): Event
    {
        if (isset($data['title']) && $data['title'] !== $event->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $event->id);
        }

        if ($image) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $image->store('events', 'public');
        }

        $event->update($data);

        return $event->fresh();
    }

    public function publish(Event $event): void
    {
        if ($event->status !== EventStatus::Draft) {
            throw new \Exception('Event not in draft state');
        }

        $event->update(['status' => EventStatus::Published]);
    }

    public function cancel(Event $event): void
    {
        $event->update(['status' => EventStatus::Cancelled]);
    }

    public function delete(Event $event): void
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();
    }

    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (
            Event::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$original}-{$count}";
            ++$count;
        }

        return $slug;
    }
}