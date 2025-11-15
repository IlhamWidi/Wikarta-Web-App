# 🎯 SETUP TASK SCHEDULER - COPY PASTE READY

Task Scheduler sudah dibuka. Ikuti langkah ini:

## 📝 LANGKAH-LANGKAH (Copy-Paste Ready)

### 1. Create Basic Task
- Klik **Action** → **Create Basic Task**
- Name: `Wikarta Laravel Scheduler`
- Description: `Laravel schedule:run every minute`
- Click **Next**

### 2. Trigger
- Pilih: **Daily**
- Start: **Hari ini**
- Click **Next**

### 3. Action
- Pilih: **Start a program**
- Click **Next**

### 4. Program/Script (COPY INI 👇)

**Program/script:**
```
C:\laragon\bin\php\php-8.4.8-nts-Win32-vs17-x64\php.exe
```

**Add arguments (optional):**
```
artisan schedule:run
```

**Start in (optional):**
```
C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web
```

- Click **Next**
- ✅ CHECK: **Open the Properties dialog for this task when I click Finish**
- Click **Finish**

### 5. Properties Dialog Muncul

#### Tab: Triggers
- **Double-click** trigger yang ada
- ✅ CHECK: **Repeat task every:** → Pilih `1 minute`
- ✅ CHECK: **for a duration of:** → Pilih `Indefinitely`
- Click **OK**

#### Tab: Settings
- ✅ CHECK: **Allow task to be run on demand**
- ✅ CHECK: **Run task as soon as possible after a scheduled start is missed**
- **If the task is already running:** → Pilih `Do not start a new instance`
- Click **OK**

### 6. Test
- Klik kanan task → **Run**
- Refresh (F5)
- Lihat **Last Run Result** → harus `0x0` (success)

---

## ✅ SELESAI!

Kalau sudah, beritahu saya: **"scheduler sudah jalan"**

Lalu saya lanjut bantu yang lain! 🚀
