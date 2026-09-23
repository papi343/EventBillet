<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'Admin';
    case Participant = 'Participant';
    case Organiser = 'Organiser';
  
}
