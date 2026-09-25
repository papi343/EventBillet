<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specified ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Admin 
            || $user->id === $ticket->user_id 
            || $user->id === $ticket->event->organizer_id;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Admin 
            || $user->id === $ticket->event->organizer_id;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Admin 
            || $user->id === $ticket->event->organizer_id;
    }

    /**
     * Determine whether the user can scan the ticket.
     * Only the organizer of the ticket's event has permission to scan.
     */
    public function scan(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::Organiser && $user->id === $ticket->event->organizer_id;
    }
}

