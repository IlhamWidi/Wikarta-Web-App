# 📱 Panduan Dunning Notification - Wikarta Provider

## 🎯 Apa itu Dunning Notification?

Sistem otomatis untuk mengirim pengingat pembayaran kepada pelanggan melalui **Email** dan **WhatsApp** pada waktu yang tepat sebelum dan sesudah jatuh tempo tagihan.

---

## 📅 Tahapan Pengingatan

| Tahap | Waktu | Deskripsi |
|-------|-------|-----------|
| **T-7** | 7 hari sebelum jatuh tempo | Pengingat awal |
| **T-3** | 3 hari sebelum jatuh tempo | Pengingat kedua |
| **T-1** | 1 hari sebelum jatuh tempo | Pengingat terakhir sebelum jatuh tempo |
| **T+3** | 3 hari setelah jatuh tempo | Peringatan tunggakan |

---

## 📨 Format Pesan

### Subject Email:
```
Pengingat Pembayaran Tagihan INV-20251115-123456 🕒
```

### Isi Pesan (Email & WhatsApp):
```
Halo [Nama Customer],

Kami ingin mengingatkan bahwa tagihan INV-20251115-123456 sebesar Rp 150.000 akan jatuh tempo pada 18 November 2025.
Mohon segera menyelesaikan pembayaran agar layanan Anda tetap aktif tanpa gangguan.

Jika Anda sudah melakukan pembayaran, abaikan pesan ini.
Terima kasih atas kerja sama dan kepercayaannya.

Salam,
Tim Wikarta Provider
```

---

## 🔧 Setup Production

### 1. Aktifkan Scheduler (Wajib!)

**Windows (Task Scheduler):**
1. Buka Task Scheduler
2. Create Basic Task
3. Nama: `Laravel Scheduler - Wikarta`
4. Trigger: **Daily**
5. Time: **Every 1 minute** (atau sesuai kebutuhan)
6. Action: **Start a program**
7. Program: `php`
8. Arguments: `"C:\path\to\artisan" schedule:run`
9. Start in: `C:\path\to\project`

**Linux/Mac (Crontab):**
```bash
# Edit crontab
crontab -e

# Tambahkan baris ini
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Setup Queue Worker (Wajib!)

Update `.env`:
```env
QUEUE_CONNECTION=database
```

Jalankan queue worker (biarkan running):
```bash
php artisan queue:work
```

**Untuk Production (supervisor):**
```bash
# Install supervisor (Linux)
sudo apt-get install supervisor

# Buat config: /etc/supervisor/conf.d/wikarta-worker.conf
[program:wikarta-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start wikarta-worker:*
```

### 3. Setup Email SMTP (Opsional tapi Direkomendasikan)

Update `.env` untuk email production:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@wikarta.com
MAIL_FROM_NAME="Wikarta Provider"
```

---

## 🧪 Testing

### Test WhatsApp (dengan konfirmasi)
```bash
php artisan whatsapp:test "085707211646" "Halo, test WhatsApp!"
```

Output:
```
⚠️  Ini akan mengirim WhatsApp ke 085707211646. Lanjutkan? (yes/no) [yes]:
> yes
Mengirim WhatsApp ke: 085707211646
Pesan: Halo, test WhatsApp!
✅ WhatsApp berhasil dikirim!
```

### Test Dunning Notification (dengan konfirmasi)
```bash
php artisan dunning:test "customer@email.com" "085707211646"
```

Output:
```
⚠️  Ini akan mengirim WhatsApp & Email ke customer. Lanjutkan? (yes/no) [yes]:
> yes
🔧 Membuat customer test...
✅ Menggunakan customer existing: Dimas Pratama C (ID: 2)
🧾 Membuat invoice test...
✅ Invoice created: INV-20251115-123456
📱 Mengirim dunning notification (T-3)...
✅ Job dispatched!
```

### Test Scheduler Manual
```bash
php artisan dunning:run
```

Output:
```
🔄 Starting dunning scheduler...
📅 Stage T-7 (2025-11-22): Found 0 invoices
📅 Stage T-3 (2025-11-18): Found 6 invoices
   ⏭️  Skipped INV-20251115-123456 (already sent)
   ✅ Dispatched INV-20251115-789012 to John Doe
📅 Stage T-1 (2025-11-16): Found 0 invoices
📅 Stage T+3 (2025-11-12): Found 1 invoices
   ✅ Dispatched INV-20251110-345678 to Jane Smith

✅ Dunning scheduler completed:
   📤 Dispatched: 2
   ⏭️  Skipped: 1
```

---

## 🛡️ Proteksi Anti-Spam

### 1. Konfirmasi Manual
Setiap command test akan meminta konfirmasi sebelum mengirim pesan untuk mencegah spam tidak sengaja.

### 2. Deteksi Duplikasi
Sistem otomatis mencegah pengiriman duplikat dengan mengecek `notifications` table. Satu invoice hanya akan menerima 1 notifikasi per stage per hari.

### 3. Rate Limiting
- Fonnte Free: **1000 pesan/bulan**
- Pastikan quota cukup sebelum produksi

---

## 📊 Monitoring

### Cek Log Notifikasi
```bash
# Windows
Get-Content "storage\logs\laravel.log" -Tail 50 | Select-String "WhatsApp"

# Linux/Mac
tail -f storage/logs/laravel.log | grep WhatsApp
```

### Cek Database
```sql
-- Lihat notifikasi terakhir
SELECT * FROM notifications 
ORDER BY created_at DESC 
LIMIT 10;

-- Lihat notifikasi yang gagal
SELECT * FROM notifications 
WHERE status = 'failed' 
ORDER BY created_at DESC;

-- Statistik notifikasi hari ini
SELECT 
    channel,
    status,
    COUNT(*) as total
FROM notifications
WHERE DATE(created_at) = CURDATE()
GROUP BY channel, status;
```

---

## ⚠️ Troubleshooting

### WhatsApp Gagal Terkirim

**Error: "request invalid on disconnected device"**
- **Penyebab:** Nomor WhatsApp tidak terdaftar/tidak aktif di Fonnte
- **Solusi:** 
  1. Login ke [fonnte.com](https://fonnte.com)
  2. Pastikan device sudah di-scan QR code
  3. Status device harus "Connected"

**Error: "Quota exceeded"**
- **Penyebab:** Quota Fonnte habis (1000 pesan/bulan untuk free)
- **Solusi:** Upgrade paket atau tunggu reset bulanan

### Scheduler Tidak Jalan

**Cek apakah scheduler terdaftar:**
```bash
php artisan schedule:list
```

Harus muncul:
```
0 8 * * * php artisan dunning:run .......... Next Due: ...
```

**Jika tidak muncul:**
1. Cek `app/Console/Kernel.php`
2. Pastikan ada: `$schedule->command('dunning:run')->dailyAt('08:00');`
3. Clear cache: `php artisan config:clear`

### Queue Tidak Process

**Cek queue worker running:**
```bash
ps aux | grep "queue:work"  # Linux/Mac
Get-Process | Where-Object {$_.ProcessName -like "*php*"}  # Windows
```

**Jika tidak ada:**
```bash
php artisan queue:work
```

**Untuk production, gunakan supervisor!**

---

## 📈 Best Practices

1. ✅ **Setup Scheduler** - Wajib untuk otomatis
2. ✅ **Setup Queue Worker** - Wajib untuk background processing
3. ✅ **Setup SMTP Email** - Opsional tapi profesional
4. ✅ **Monitor Log** - Cek error secara berkala
5. ✅ **Backup Database** - Terutama `notifications` table
6. ✅ **Test Sebelum Production** - Gunakan command test
7. ✅ **Check Quota Fonnte** - Pastikan tidak habis mendadak

---

## 🎯 Jadwal Harian (Production)

```
00:00 - Backup database otomatis
08:00 - Dunning scheduler jalan (T-7, T-3, T-1, T+3)
09:00 - Cek log untuk error
Continuous - Queue worker process jobs
```

---

## 📞 Support

Jika ada masalah:
1. Cek log: `storage/logs/laravel.log`
2. Cek database: `notifications` table
3. Test manual: `php artisan whatsapp:test`
4. Lihat dokumentasi Fonnte: https://fonnte.com/api

---

**Dibuat oleh:** GitHub Copilot  
**Terakhir Update:** 15 November 2025  
**Versi:** 1.0.0
