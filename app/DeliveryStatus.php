<?php

namespace App;

enum DeliveryStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Delivered = 'delivered';
}
