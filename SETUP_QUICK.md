# 🚀 SETUP TASK SCHEDULER - QUICK GUIDE

## ⚡ Langkah Cepat (5 Menit)

### 1. Buka Task Scheduler as Administrator
```powershell
# Klik kanan PowerShell → Run as Administrator
# Lalu jalankan:
taskschd.msc
```

### 2. Create Basic Task
- Klik **Action** → **Create Basic Task**
- Name: `Wikarta Laravel Scheduler`
- Click **Next**

### 3. Trigger
- Select: **Daily**
- Start: **Today**
- Click **Next**

### 4. Action
- Select: **Start a program**
- Click **Next**

### 5. Program Details

**COPY-PASTE INI:**

Program/script:
```
C:\laragon\bin\php\php-8.4.8-nts-Win32-vs17-x64\php.exe
```

Add arguments:
```
"C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web\artisan" schedule:run
```

Start in:
```
C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web
```

- Click **Next**
- ✅ Check: **Open the Properties dialog for this task when I click Finish**
- Click **Finish**

### 6. Properties Dialog

**Tab: Triggers**
- Double-click trigger
- ✅ Check: **Repeat task every:** `1 minute`
- ✅ For a duration of: `Indefinitely`
- Click **OK**

**Tab: General**
- ✅ Check: **Run whether user is logged on or not**
- ✅ Check: **Run with highest privileges**

**Tab: Settings**
- ✅ Check: **Allow task to be run on demand**
- ✅ Check: **Run task as soon as possible after a scheduled start is missed**
- If the task is already running: **Do not start a new instance**

- Click **OK**
- Enter Windows password if prompted

### 7. Verify
- Klik kanan task → **Run**
- Check **History** tab → harus ada log

---

## ✅ DONE!

Sekarang scheduler jalan otomatis setiap 1 menit!

**Test:**
```bash
php artisan schedule:list
# Harus muncul: dunning:run ... Next Due: ...
```

**Monitor:**
```bash
# Cek log
Get-Content storage\logs\laravel.log -Tail 20 -Wait
```

**Jadwal Dunning:** Setiap hari jam 08:00 WIB

---

## 🔧 Troubleshooting

**Task tidak jalan?**
1. Klik kanan task → Properties
2. Tab Actions → Pastikan path PHP benar
3. Tab Triggers → Pastikan "Repeat every 1 minute" aktif
4. Tab History → Lihat error log

**PHP path salah?**
```powershell
# Cari PHP
where.exe php

# Update path di Task Scheduler Actions
```

