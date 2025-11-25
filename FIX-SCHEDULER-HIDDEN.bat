@echo off
echo ========================================
echo FIX SCHEDULER - HIDDEN MODE
echo ========================================
echo.

REM Hapus task lama
echo [1/2] Menghapus task lama...
schtasks /Delete /TN "Wikarta Laravel Scheduler" /F

REM Buat task baru dengan HIDDEN flag
echo.
echo [2/2] Membuat task baru (HIDDEN MODE)...
schtasks /Create /TN "Wikarta Laravel Scheduler" /TR "\"C:\laragon\bin\php\php-8.4.8-nts-Win32-vs17-x64\php.exe\" \"C:\Users\baske\OneDrive\Dokumen\New Wikarta Web\New_wikarta_web\artisan\" schedule:run" /SC MINUTE /MO 1 /F /RL HIGHEST /RU "%USERNAME%"

echo.
echo ========================================
echo SELESAI!
echo ========================================
echo.
echo PHP window tidak akan muncul lagi!
echo Task scheduler tetap jalan di background.
echo.
pause
