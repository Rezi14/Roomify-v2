<?php

namespace App\Enums;

enum StatusPemesanan: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CHECKED_IN = 'checked_in';
    case CHECKED_OUT = 'checked_out';
    case CANCELLED = 'cancelled';
    case PAID = 'paid';
}
