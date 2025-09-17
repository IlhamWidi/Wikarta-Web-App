<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    public const TEMPLATE_WA_INVOICE_PACITAN = "Halo pelanggan setia kak [name] 😊

Kami informasikan bahwa pembayaran WiFi sebesar Rp. [amount] jatuh tempo pada tanggal [due_date].

🎁 Kabar Gembira!
Jika Anda melakukan pembayaran ontime (tanggal 1 s.d 10 setiap bulan), Anda akan mendapatkan gift spesial dari kami!
❗ Namun, jika pembayaran dilakukan setelah tanggal 10, maka gift tidak dapat diberikan.

📍Pembayaran WiFi & pengambilan gift (untuk pelanggan yang membayar ontime) dapat dilakukan langsung di Toko Bu Murni, cukup tunjukkan pesan WA ini ya! 🛍️

💳 Anda juga bisa melakukan pembayaran melalui link berikut ini:
👉 [invoice_link]

Terima kasih atas perhatian dan kerjasamanya 🙏

Salam hangat,
Wijaya Karya Arta";

    public const TEMPLATE_WA_INVOICE_GENERAL = "📢 Hi Kak [nama]
Tagihan [paket] sebesar [nominal] akan jatuh tempo pada tanggal  [tanggal].

🕓 Bayar tepat waktu (tanggal 1–5) akan mendapatkan gift dari kami 🚀

💳 Silakan klik link berikut untuk melakukan pembayaran dengan mudah:
👉 [link]

📌 Mohon simpan nomor WhatsApp ini, agar Kakak bisa tetap menerima info tagihan dan notifikasi pembayaran ke depannya.

Jika ada kendala atau pertanyaan, langsung aja hubungi kami lewat WhatsApp ini atau email ke: 📧 cs@wikarta.co.id

Terima kasih sudah menggunakan layanan Wikarta Internet! 😊";

    public const TEMPLATE_WA_INVOICE_UMUM = "📢 Hi Kak [nama]

Tagihan [paket] sebesar [nominal] akan jatuh tempo pada tanggal [tanggal].

🕓 Bayar tepat waktu (antara tanggal 1–5) dan dapatkan gift spesial dari kami! 🎁
Catatan: Gift hanya berlaku untuk tagihan di atas Rp150.000.

💳 Klik link berikut untuk melakukan pembayaran dengan mudah dan cepat:
👉 [link]

📌 Jangan lupa simpan nomor WhatsApp ini, agar Kakak tetap menerima info tagihan & notifikasi pembayaran berikutnya ya!

Jika ada pertanyaan atau kendala, langsung aja hubungi kami di WhatsApp ini, atau email ke:
📧 cs@wikarta.co.id

🙏 Terima kasih sudah menjadi bagian dari Wikarta Internet! Semoga koneksi Kakak selalu lancar! 😊";
}
