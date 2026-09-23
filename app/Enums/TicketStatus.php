<?php
namespace App\Enums;

enum TicketStatus: string {
    case used = "used";
    case unused = "unused";
}
