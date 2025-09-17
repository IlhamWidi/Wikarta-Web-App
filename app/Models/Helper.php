<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Helper extends Model
{
    use HasFactory;

    public static function str_to_slug($string)
    {
        $clean = strtolower(str_replace(" ", "_", $string));
        $clean = strtolower(str_replace("%", "_", $clean));
        $clean = strtolower(str_replace("#", "_", $clean));
        $clean = strtolower(str_replace("!", "_", $clean));
        $clean = strtolower(str_replace("?", "_", $clean));
        return $clean;
    }

    public static function DateDiff2Dates($start_date, $end_date)
    {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        if ($start < $end) {
            return "00:00:00";
        }
        $diff = $end->diff($start);
        $hours = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;

        $time_format =  str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':' . str_pad($seconds, 2, "0", STR_PAD_LEFT);
        return $time_format;
    }

    public static function FormatPhoneNumber($phone_number)
    {
        $phone_number = trim($phone_number);
        $phone_number = rtrim($phone_number);
        $phone_number = preg_replace('/\s+/', '', $phone_number);
        $phone_number = strip_tags($phone_number);
        $phone_number = str_replace(" ", "", $phone_number);
        $phone_number = str_replace("+", "", $phone_number);
        $phone_number = str_replace("(", "", $phone_number);
        $phone_number = str_replace(")", "", $phone_number);
        $phone_number = str_replace(".", "", $phone_number);
        $phone_number = str_replace("-", "", $phone_number);
        $phone_number = str_replace("\u202c", "", $phone_number);
        $phone_number = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $phone_number);

        if (substr(trim($phone_number), 0, 3) == '62') {
            $phone_number = trim($phone_number);
        } elseif (substr($phone_number, 0, 1) == '0') {
            $phone_number = '62' . substr($phone_number, 1);
        }
        return $phone_number;
    }
}
