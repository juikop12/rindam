@echo off
title Buka Firewall Windows untuk Akses LAN / Wi-Fi
chcp 65001 >nul
cls

echo ======================================================================
echo   SIPANDU-WBK - BUKA PORT FIREWALL WINDOWS (PORT 80 & 8000)
echo ======================================================================
echo.
echo  Skrip ini memerlukan Hak Akses Administrator.
echo  Mengecek izin Administrator...
echo.

net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [PERINGATAN] Skrip TIDAK dijalankan sebagai Administrator!
    echo Silakan KLIK KANAN file ini lalu pilih "Run as administrator" / "Jalankan sebagai administrator".
    echo.
    pause
    exit /b 1
)

echo [OK] Izin Administrator terverifikasi.
echo.
echo Mendaftarkan izin Port 80 (Apache) ke Windows Defender Firewall...
netsh advfirewall firewall delete rule name="Laragon Apache (Port 80)" >nul 2>&1
netsh advfirewall firewall add rule name="Laragon Apache (Port 80)" dir=in action=allow protocol=TCP localport=80 profile=any
echo [OK] Port 80 berhasil dibuka untuk semua profil jaringan.

echo.
echo Mendaftarkan izin Port 8000 (Artisan Serve) ke Windows Defender Firewall...
netsh advfirewall firewall delete rule name="Laravel Dev Server (8000)" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel Dev Server (8000)" dir=in action=allow protocol=TCP localport=8000 profile=any
echo [OK] Port 8000 berhasil dibuka untuk semua profil jaringan.

echo.
echo ======================================================================
echo   SUKSES! Port 80 dan Port 8000 sekarang terbuka penuh di Firewall.
echo   Perangkat lain di Wi-Fi sekarang dapat mengakses aplikasi web ini.
echo ======================================================================
echo.
pause
