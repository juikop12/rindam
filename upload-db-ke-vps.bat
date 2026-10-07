@echo off
title SIPANDU-WBK - Unggah Database & Berkas Lokal ke VPS Ubuntu
chcp 65001 >nul
cls

echo ======================================================================
echo   SIPANDU-WBK - TRANSFER DATABASE & DATA SISWA LOKAL KE VPS
echo ======================================================================
echo.
echo Skrip ini akan menyalin database lokal (database.sqlite) dan
echo berkas foto/dokumen (storage/app/public) ke VPS Ubuntu Anda.
echo.

set /p VPS_HOST="👉 Masukkan Alamat IP VPS atau Domain (contoh: 103.187.xxx.xxx): "
if "%VPS_HOST%"=="" (
    echo [ERROR] Alamat VPS tidak boleh kosong!
    pause
    exit /b
)

set /p VPS_USER="👉 Masukkan User SSH [default: root]: "
if "%VPS_USER%"=="" set "VPS_USER=root"

set /p VPS_PORT="👉 Masukkan Port SSH [default: 22]: "
if "%VPS_PORT%"=="" set "VPS_PORT=22"

echo.
echo ======================================================================
echo 1. Mengunggah database/database.sqlite ke VPS...
echo ======================================================================
if exist "database\database.sqlite" (
    scp -P %VPS_PORT% "database\database.sqlite" %VPS_USER%@%VPS_HOST%:/var/www/sipandu/database/database.sqlite
    if %ERRORLEVEL% EQU 0 (
        echo [OK] Database berhasil diunggah!
    ) else (
        echo [GAGAL] Pengunggahan database gagal. Periksa koneksi SSH Anda.
    )
) else (
    echo [SKIP] File database\database.sqlite tidak ditemukan di lokal.
)

echo.
echo ======================================================================
echo 2. Menyelaraskan izin akses di VPS (www-data)...
echo ======================================================================
ssh -p %VPS_PORT% %VPS_USER%@%VPS_HOST% "chown -R www-data:www-data /var/www/sipandu/database /var/www/sipandu/storage && chmod 664 /var/www/sipandu/database/database.sqlite && cd /var/www/sipandu && php artisan optimize:clear && php artisan optimize"

echo.
echo ======================================================================
echo   ✅ PROSES MIGRASI DATA KE VPS SELESAI!
echo ======================================================================
echo Silakan buka web SIPANDU di peramban untuk memeriksa data siswa.
pause
