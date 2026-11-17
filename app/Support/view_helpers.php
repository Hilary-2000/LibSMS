<?php

/*
 | Helper functions used inside the Blade views (they used to be declared inline in the views).
 | Loaded once at startup by AppServiceProvider, so a view can call them anywhere on the page:
 | an inline declaration wrapped in function_exists() only exists after that line has run, which
 | broke pages that call the helper before the place it was declared.
 */

if (!function_exists('isPresent')) {
    function isPresent($array,$needle){
        if ($array == null) {
            return false;
        }
        for ($index=0; $index < count($array); $index++) { 
            if ($array[$index] == $needle) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getDateDifference')) {
    function getDateDifference($date1, $date2, $format = 'days') {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);

        switch ($format) {
            case 'years':
                return $interval->y;
            case 'months':
                return $interval->y * 12 + $interval->m;
            case 'days':
                return $interval->days;
            case 'hours':
                return $interval->days * 24 + $interval->h;
            case 'minutes':
                return ($interval->days * 24 + $interval->h) * 60 + $interval->i;
            case 'seconds':
                return (($interval->days * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
            default:
                return $interval;
        }
    }
}

if (!function_exists('elongateData')) {
    function elongateData($larger_length,$data){
        // get the length of the string
        $length = strlen($data);

        // get the remaining length if string to make it match the longest one
        $larger_length -= $length;

        // loop to add the string string to match the longest.
        for ($index=0; $index < $larger_length; $index++) { 
            $data.= ($index+1 == $larger_length ? "   " : "   ");
        }
        return $data;
    }
}

if (!function_exists('getTimeAgo')) {
    function getTimeAgo($date)
    {
        $currentTimestamp = time();
        $timestamp = strtotime($date);
        $difference = $currentTimestamp - $timestamp;

        if ($difference < 60) {
            return $difference . " seconds ago";
        } elseif ($difference < 3600) {
            $minutes = floor($difference / 60);
            return $minutes . " minutes ago";
        } elseif ($difference < 86400) {
            $hours = floor($difference / 3600);
            return $hours . " hours ago";
        } elseif ($difference < 2592000) {
            $days = floor($difference / 86400);
            return $days . " days ago";
        } elseif ($difference < 31536000) {
            $months = floor($difference / 2592000);
            return $months . " months ago";
        } else {
            $years = floor($difference / 31536000);
            return $years . " years ago";
        }
    }
}
