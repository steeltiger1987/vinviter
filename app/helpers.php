<?php

function timezone_list() {
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $all_timezones = [];
        $now = new DateTime();

        $all_timezones = DateTimeZone::listIdentifiers(DateTimeZone::EUROPE);
        $all_timezones = array_merge($all_timezones, DateTimeZone::listIdentifiers(DateTimeZone::ATLANTIC));
        $all_timezones = array_merge($all_timezones, DateTimeZone::listIdentifiers(DateTimeZone::AMERICA));


        foreach ($all_timezones as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_UTC_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

function format_UTC_offset($offset) {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'UTC' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}
function format_timezone_name($name) {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}
