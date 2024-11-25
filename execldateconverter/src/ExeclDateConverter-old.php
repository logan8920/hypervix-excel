<?php

/**
 * 
 */

namespace Hiypervix;

use DateTime;
class ExeclDateConverterOLD
{
    public static $date;
    public $strToTime;

    public function __construct($strToTime = '')
    {
        $this->strToTime = $strToTime;
    }

    /**
     * Function to convert the Excel Date 
     * @param mixed $date_value
     * @return ExeclDateConverter|null
     **/
    public static function date($date_value = null) : ?ExeclDateConverter
    {
        if (!$date_value) 
            return NULL;

        self::$date = $date_value;
        // Handle Excel date values
        if (is_numeric($date_value)) {
            /**
             * Number of days between the beginning of serial date-time (1900-Jan-0)
             * used by Excel and the beginning of UNIX Epoch time (1970-Jan-1).
             */
            $days_since_1900 = 25569;

            if ($date_value < 60) {
                --$days_since_1900;
            }

            /**
             * Values greater than 1 contain both a date and time while values lesser
             * than 1 contain only a fraction of a 24-hour day, and thus only time.
             */
            if ($date_value >= 1) {
                $utc_days = $date_value - $days_since_1900;
                $timestamp = round($utc_days * 86400);

                if (($timestamp <= PHP_INT_MAX) && ($timestamp >= -PHP_INT_MAX)) {
                    $timestamp = (int) $timestamp;
                }
            } else {
                $hours = round($date_value * 24);
                $mins = round($date_value * 1440) - round($hours * 60);
                $secs = round($date_value * 86400) - round($hours * 3600) - round($mins * 60);
                $timestamp = (int) gmmktime($hours, $mins, $secs);
            }

            self::$date = $timestamp;
            return new ExeclDateConverter($timestamp);
        } 
       
        // Handle standard date formats
        $date_formats = [
            'd-m-Y',        // 31-12-2023
            'Y-m-d',        // 2023-12-31
            'Y/m/d',        // 2023/12/31
            'd/m/Y',        // 31/12/2023
            'Y/d/m',        // 2023/31/12
            'd.m.Y',        // 31.12.2023
            'm-d-Y',        // 12-31-2023
            'm/d/Y',        // 12/31/2023
            'd M Y',        // 31 Dec 2023
            'd F Y',        // 31 December 2023
            'M d, Y',       // Dec 31, 2023
            'F d, Y',       // December 31, 2023
            'd-M-Y',        // 31-Dec-2023
            'd-F-Y',        // 31-December-2023
            'Y-M-d',        // 2023-Dec-31
            'Y-F-d',        // 2023-December-31
            'D, d M Y',     // Sun, 31 Dec 2023
            'l, d F Y',     // Sunday, 31 December 2023
            'd F, Y',       // 31 December, 2023
            'd-M-y',        // 31-Dec-23
            'Y/m/d H:i:s',  // 2023/12/31 23:59:59 (with time)
            'd-m-Y H:i',    // 31-12-2023 23:59 (with time)
            'Y.m.d',        // 2023.12.31
            'm.d.Y',        // 12.31.2023
            'j/n/Y',        // 31/12/2023 (no leading zero in day/month)
            'n/j/Y',        // 12/31/2023 (no leading zero in day/month)
            'd M, Y H:i',   // 31 Dec, 2023 23:59 (with time)
            'd F, Y H:i:s', // 31 December, 2023 23:59:59 (with time)
        ];

        foreach ($date_formats as $format) {
            $date = DateTime::createFromFormat($format, trim($date_value));
        
            if ($date) {
                self::$date = $date->getTimestamp();
                return new ExeclDateConverter(self::$date);
            }
        }

        return NULL;
    }

    /**
     * Function to format the date 
     * @param string|null $format
     * @return string|null
     **/
    public static function format($format = null) : ?string
    {
        if (!self::$date) 
            return NULL;

        return $format ? date($format, self::$date) : self::$date;
    }
}


