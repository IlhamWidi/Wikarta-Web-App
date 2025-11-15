# 📅 Setup Scheduler Windows - Wikarta Provider

## 🎯 Tujuan
Mengaktifkan Laravel Scheduler agar dunning notification jalan otomatis setiap hari jam 08:00 WIB.

---

## 🪟 **Cara 1: Task Scheduler (RECOMMENDED untuk Production)**

### Langkah 1: Buka Task Scheduler
1. Tekan `Win + R`
2. Ketik `taskschd.msc`
3. Enter

### Langkah 2: Create Basic Task
1. Klik kanan di panel kiri → **Create Basic Task...**
2. Name: `Wikarta Provider - Laravel Scheduler`
3. Description: `Menjalankan Laravel scheduler setiap menit untuk dunning notification`
4. Click **Next**

### Langkah 3: Trigger
1. Select: **Daily**
2. Click **Next**
3. Start: **Today's date**
4. Recur every: **1 days**
5. Click **Next**

### Langkah 4: Action
1. Select: **Start a program**
2. Click **Next**

### Langkah 5: Program Details
```
Program/script: C:\xampp\php\php.exe
(sesuaikan dengan lokasi PHP kamu)

Add arguments:
C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web\artisan schedule:run

Start in:
C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web
```

3. Click **Next**
4. Check "Open the Properties dialog..." 
5. Click **Finish**

### Langkah 6: Advanced Settings (di Properties Dialog)
1. Tab **General**:
   - ✅ Run whether user is logged on or not
   - ✅ Run with highest privileges
   
2. Tab **Triggers**:
   - Double click trigger yang ada
   - ✅ Enabled
   - Repeat task every: **1 minute**
   - for a duration of: **Indefinitely**
   - Click **OK**

3. Tab **Settings**:
   - ✅ Allow task to be run on demand
   - ✅ Run task as soon as possible after a scheduled start is missed
   - If the task is already running: **Do not start a new instance**
   
4. Click **OK**
5. Masukkan password Windows kamu jika diminta

---

## 🖥️ **Cara 2: PowerShell Script (untuk Development)**

### Setup Script
Buat file `run-scheduler.ps1`:

```powershell
# run-scheduler.ps1
$projectPath = "C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web"
$phpPath = "C:\xampp\php\php.exe"

while ($true) {
    & $phpPath "$projectPath\artisan" schedule:run
    Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Scheduler executed"
    Start-Sleep -Seconds 60
}
```

### Jalankan Script
```powershell
# Buka PowerShell
cd "C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web"

# Jalankan script (akan loop setiap 1 menit)
.\run-scheduler.ps1
```

**Catatan:** Biarkan PowerShell tetap terbuka!

---

## 🧪 **Testing**

### Test Manual
```bash
# Jalankan scheduler sekali
php artisan schedule:run

# Output:
# No scheduled tasks are ready to run.
# (jika belum jam 08:00)

# atau

# Running scheduled command: php artisan dunning:run
# (jika sudah jam 08:00)
```

### Lihat Jadwal Terdaftar
```bash
php artisan schedule:list

# Output:
# 0 8 * * * php artisan dunning:run .......... Next Due: Tomorrow at 08:00
```

### Test Dunning Manual
```bash
php artisan dunning:run

# Output:
# 🔄 Starting dunning scheduler...
# 📅 Stage T-7 (2025-11-22): Found 0 invoices
# 📅 Stage T-3 (2025-11-18): Found 2 invoices
#    ✅ Dispatched INV-123 to John Doe
#    ⏭️  Skipped INV-456 (already sent)
# ✅ Dunning scheduler completed:
#    📤 Dispatched: 1
#    ⏭️  Skipped: 1
```

---

## 📊 **Monitoring**

### Cek Task Scheduler Berjalan
1. Buka Task Scheduler
2. Cari "Wikarta Provider" di daftar
3. Tab **History** untuk lihat log

### Cek Log Laravel
```powershell
# Windows PowerShell
Get-Content "storage\logs\laravel.log" -Tail 50 -Wait

# Cari text:
# [YYYY-MM-DD HH:MM:SS] local.INFO: ...
```

### Cek Database
```sql
SELECT * FROM notifications 
WHERE type = 'invoice.dunning' 
AND DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

---

## ⚠️ **Troubleshooting**

### Task Scheduler tidak jalan

**Problem:** Task ada di list tapi tidak execute

**Solusi:**
1. Klik kanan task → **Properties**
2. Tab **General** → pastikan user account benar
3. Tab **Triggers** → pastikan "Repeat task every 1 minute" aktif
4. Tab **Actions** → pastikan path PHP dan artisan benar
5. Klik kanan task → **Run** untuk test manual

### PHP tidak ditemukan

**Problem:** "The system cannot find the file specified"

**Solusi:**
```powershell
# Cari lokasi PHP
where.exe php

# Atau cek XAMPP/Laragon
# XAMPP: C:\xampp\php\php.exe
# Laragon: C:\laragon\bin\php\php-8.x\php.exe

# Update path di Task Scheduler
```

### Scheduler jalan tapi tidak ada notifikasi

**Cek:**
1. Queue connection: Harus `database` (bukan `sync`)
2. Queue worker running: `php artisan queue:work`
3. Log Laravel: `storage/logs/laravel.log`
4. Timezone: Harus `Asia/Jakarta`

---

## ✅ **Verification Checklist**

- [ ] Task Scheduler terdaftar
- [ ] Trigger: Daily, repeat every 1 minute
- [ ] Action: Correct PHP path & artisan path
- [ ] Status: **Running** (bukan Disabled)
- [ ] History: Ada log execution
- [ ] `php artisan schedule:list` menampilkan `dunning:run`
- [ ] Queue worker running: `php artisan queue:work`
- [ ] Timezone: `Asia/Jakarta` di `php artisan about`

---

## 🎉 **Success Indicators**

Scheduler berhasil jika:

1. ✅ Task Scheduler ada di Windows
2. ✅ Task running setiap 1 menit (cek History)
3. ✅ `php artisan schedule:list` tampil jadwal
4. ✅ Jam 08:00 WIB, dunning:run otomatis jalan
5. ✅ Notifikasi WhatsApp terkirim (cek Fonnte)
6. ✅ Database `notifications` ada log baru

---

**Dibuat oleh:** GitHub Copilot  
**Tanggal:** 15 November 2025  
**Untuk:** Wikarta Provider - Dunning Notification System
