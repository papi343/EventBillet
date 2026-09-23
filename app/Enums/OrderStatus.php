<?php

namespace App\Enums;

enum OrderStatus:string{
    case pending = "pending";
    case paid = "paid";
    case failed = "failled"
    case refunded = "refunded"

}