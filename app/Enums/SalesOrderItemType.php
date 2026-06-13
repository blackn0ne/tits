<?php

namespace App\Enums;

enum SalesOrderItemType: string
{
    case Product = 'product';
    case Service = 'service';
}
