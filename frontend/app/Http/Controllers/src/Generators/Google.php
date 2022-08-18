<?php

namespace App\Http\Controllers\src\Generators;

use App\Http\Controllers\src\Generator;
use App\Http\Controllers\src\Link;
use DateTimeZone;

/**
 * @see https://github.com/InteractionDesignFoundation/add-event-to-calendar-docs/blob/master/services/google.md
 */
class Google implements Generator
{
    /** @var string {@see https://www.php.net/manual/en/function.date.php} */
    protected $dateFormat = 'Ymd';
    protected $dateTimeFormat = 'Ymd\THis';

    /** {@inheritDoc} */
    public function generate(Link $link): string
    {
        $url = 'https://calendar.google.com/calendar/render?action=TEMPLATE';

        $utcStartDateTime = (clone $link->from)->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $utcEndDateTime = (clone $link->to)->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $dateTimeFormat = $link->allDay ? $this->dateFormat : $this->dateTimeFormat;
        $url .= '&dates='.$utcStartDateTime->format($dateTimeFormat).'/'.$utcEndDateTime->format($dateTimeFormat);

        $url .= '&text='.urlencode($link->title);

        if ($link->description) {
            $url .= '&details='.urlencode($link->description);
        }

        if ($link->address) {
            $url .= '&location='.urlencode($link->address);
        }

        return $url;
    }
}
