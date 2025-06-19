<?php

use Carbon\CarbonInterval;

if (!function_exists('humanAbbreviatedTime')) {
    function humanAbbreviatedTime(string $duration): string
    {
        try {
            $interval = CarbonInterval::createFromFormat('H:i:s', $duration)->cascade();
            $parts = [];

            if ($interval->hours)   $parts[] = $interval->hours . 'h';
            if ($interval->minutes) $parts[] = $interval->minutes . 'm';
            if ($interval->seconds) $parts[] = $interval->seconds . 's';

            return implode(' ', $parts);
        } catch (Exception $e) {
            return $duration;
        }
    }
}