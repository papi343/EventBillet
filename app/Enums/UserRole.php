<?php

namespace App;

enum UserRole: string
{
    case Admin = 'Admin';
    case Participant = 'Participant';
    case Organiser = 'Organiser';
  
}
