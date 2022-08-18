<?php

namespace App\Http\Controllers\src\Generators;

use App\Http\Controllers\src\Generator;
use App\Http\Controllers\src\Link;

/**
 * @see https://icalendar.org/RFC-Specifications/iCalendar-RFC-5545/
 */
class Ics implements Generator
{
    /** @var string {@see https://www.php.net/manual/en/function.date.php} */
    protected $dateFormat = 'Ymd';
    protected $dateTimeFormat = 'Ymd\THis';

    /** @var array */
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /** {@inheritDoc} */
    public function generate(Link $link): string
    {
        $url = [
            "BEGIN:VCALENDAR",
            "VERSION:2.0",
            "METHOD:PUBLISH",
            "BEGIN:VEVENT",
            "UID:".($this->options["UID"] ?? $this->generateEventUid($link)),
            "SUMMARY:".$this->escapeString($link->title),
        ];

        $dateTimeFormat = $link->allDay ? $this->dateFormat : $this->dateTimeFormat;
        if ($link->allDay) {
            $url[] = "DTSTART:".$link->from->format($dateTimeFormat);
            $url[] = "DURATION:P1D";
        } else {
            $url[] = "DTSTART:".$link->from->format($dateTimeFormat);
            $url[] = "DTEND:".$link->to->format($dateTimeFormat);
        }
        if ($link->address) {
            $url[] = "LOCATION:".$this->escapeString($link->address);
        }
        if ($link->description) {
            $url[] = "DESCRIPTION:".$this->escapeString($link->description);
        }

        $url[] = "TRANSP:OPAQUE";
        $url[] = "SEQUENCE:0";
        $url[] = "DTSTAMP:".$link->from->format($dateTimeFormat);;
        $url[] = "PRIORITY:1";
        $url[] = "CLASS:PUBLIC";

        $url[] = "BEGIN:VALARM";
        $url[] = "TRIGGER:-PT15M";
        $url[] = "ACTION:DISPLAY";
        $url[] = "DESCRIPTION:Reminder";
        $url[] = "END:VALARM";

        $url[] = "END:VEVENT";
        $url[] = "END:VCALENDAR";
        $url[] = "TZID:America/Sao_Paulo";

        return $this->buildLink($url);
    }

    protected function buildLink(array $propertiesAndComponents): string
    {
        return 'data:text/calendar;charset=utf8;base64,'.base64_encode(implode("\r\n", $propertiesAndComponents));
    }

    /** @see https://tools.ietf.org/html/rfc5545.html#section-3.3.11 */
    protected function escapeString(string $field): string
    {
        return addcslashes($field, "\r\n,;");
    }

    /** @see https://tools.ietf.org/html/rfc5545#section-3.8.4.7 */
    protected function generateEventUid(Link $link): string
    {
        return md5(sprintf(
            '%s%s%s%s',
            $link->from->format(\DateTimeInterface::ATOM),
            $link->to->format(\DateTimeInterface::ATOM),
            $link->title,
            $link->address
        ));
    }
}
