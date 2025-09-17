<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    use HasFactory;

    public static function Roles()
    {
        return [
            "SUPERUSER",
            "OPERATOR",
            "MARKETING",
            "TEKNISI",
            "FINANCE"
        ];
    }

    public static function Cities()
    {
        return [
            "1" => "Surabaya (Jawa Timur)",
            "2" => "Kab.Sidoarjo (Jawa Timur)",
            "3" => "Kab.Mojokerto (Jawa Timur",
        ];
    }

    public static function Years()
    {
        return [
            2024,
            2025,
            2026
        ];
    }

    public static function Months()
    {
        return [
            1 => "Januari",
            2 => "Februari",
            3 => "Maret",
            4 => "April",
            5 => "Mei",
            6 => "Juni",
            7 => "Juli",
            8 => "Agustus",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Desember"
        ];
    }

    public static function PaymentMethods()
    {
        return [
            "CASH",
            "MIDTRANS"
        ];
    }

    public static function InvoiceStatuses()
    {
        return [
            "UNPAID",
            "SETTLEMENT"
        ];
    }

    public static function MarketPlace()
    {
        return [
            "OFFLINE",
            "SHOPEE",
            "TOKOPEDIA",
            "LAZADA"
        ];
    }

    public static function ReturStatus()
    {
        return [
            "RUSAK",
            "NORMAL",
        ];
    }

    public static function MetodePembayaran()
    {
        return [
            "VA_MANDIRI",
            "VA_BNI",
            "VA_BRI",
            "VA_PERMATA",
            "VA_CIMB",
            "QRIS-GOPAY"
        ];
    }
}
