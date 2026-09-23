<?php
namespace App\Enums;

enum PaymentStatus:string {
    case pending = "pending";
    case paid = "paid";
    case failed = "failed";
    case refunded = "refunded";
}