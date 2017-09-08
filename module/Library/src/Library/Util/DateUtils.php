<?php
namespace Library\Util;

class DateUtils
{
    const NOW = 'NOW';
    const ISO_DATE_TIME = 'Y-m-d H:m:s';
    const ISO_DATE = 'Y-m-d';


    public static function now($format = self::ISO_DATE)
    {
        date_default_timezone_set ("UTC");
        $dateTime = new \DateTime(self::NOW);
        return $dateTime->format($format);
    }

    public static function rightNow()
    {
        date_default_timezone_set ("UTC");
        $dateTime = new \DateTime(self::NOW);
        return $dateTime->format('YmdHms') . microtime();
    }
}