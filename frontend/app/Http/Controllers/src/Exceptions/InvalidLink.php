<?php

namespace App\Http\Controllers\src\Exceptions;

use DateTimeInterface;
use InvalidArgumentException;

class InvalidLink extends InvalidArgumentException
{
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public static function invalidDateRange(DateTimeInterface $to, DateTimeInterface $from): self
    {
        return new self("TO time (`{$to->format(self::DATETIME_FORMAT)}`) must be greater than FROM time (`{$from->format(self::DATETIME_FORMAT)}`)");
    }
}
